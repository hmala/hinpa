import pandas as pd
import mysql.connector
from mysql.connector import Error

def clean_price(price):
    if pd.isna(price):
        return 0.0
    if isinstance(price, str):
        # إزالة الفراغات وأي رموز غير رقمية
        price = ''.join(filter(lambda x: x.isdigit() or x == '.', price))
    try:
        return float(price)
    except (ValueError, TypeError):
        return 0.0

def import_excel_to_db(excel_file):
    try:
        # قراءة ملف CSV وتنظيف أسماء الأعمدة
        df = pd.read_csv(excel_file, encoding='utf-8')
        print(f"\nإحصائيات الملف قبل المعالجة:")
        print(f"عدد الصفوف الكلي: {len(df)}")
        print(f"عدد الأعمدة: {len(df.columns)}")
        print("\nأسماء الأعمدة:")
        print(df.columns.tolist())
        
        # تنظيف أسماء الأعمدة
        df.columns = df.columns.str.strip()
        
        # عرض توزيع البيانات
        print("\nتوزيع القيم الفريدة:")
        spec_code_col = 'specialization_code\xa0'.strip()
        service_code_col = 'service_code\xa0'.strip()
        print(f"{spec_code_col}: {df[spec_code_col].nunique()} قيمة فريدة")
        print(f"{service_code_col}: {df[service_code_col].nunique()} قيمة فريدة")
        print(f"codesv: {df['codesv'].nunique()} قيمة فريدة")
        
        # عرض عينة من البيانات
        print("\nأول 5 صفوف من البيانات:")
        print(df[[spec_code_col, service_code_col, 'codesv', 'namesv', 'price']].head())
        
        # حذف الصفوف التي تحتوي على قيم فارغة في الأعمدة المهمة
        before_rows = len(df)
        df = df.dropna(subset=[spec_code_col, service_code_col, 'codesv'])
        after_rows = len(df)
        print(f"\nتم حذف {before_rows - after_rows} صفوف فارغة")
          # إزالة التكرار
        before_rows = len(df)
        df = df.drop_duplicates([spec_code_col, service_code_col, 'codesv'])
        after_rows = len(df)
        print(f"تم حذف {before_rows - after_rows} صفوف مكررة")
          # ترتيب البيانات حسب service_code و specialization_code
        df = df.sort_values([service_code_col, spec_code_col])
        
        # إضافة عمود للترقيم التسلسلي يبدأ من 1
        df['codesv'] = range(1, 1 + len(df))
        df['codesv'] = df['codesv'].astype(str)  # تحويل الأرقام إلى نصوص
        
        print(f"\nعدد الصفوف النهائي للاستيراد: {len(df)}")
        print("\nعينة من البيانات بعد إضافة الترقيم التسلسلي:")
        print(df[[spec_code_col, service_code_col, 'codesv', 'namesv']].head())
        
        # الاتصال بقاعدة البيانات
        connection = mysql.connector.connect(
            host='localhost',
            user='root',
            password='',
            database='hinpa'
        )
        
        cursor = connection.cursor()
        
        # تحضير البيانات وإدخالها
        for index, row in df.iterrows():
            # إدخال الخدمة إذا لم تكن موجودة
            spec_code = str(row[spec_code_col]).strip() if not pd.isna(row[spec_code_col]) else ''
            cursor.execute("""
                INSERT IGNORE INTO services (sercode, sername)
                VALUES (%s, %s)
            """, (
                spec_code,
                f"خدمة {spec_code}"
            ))
            
            # الحصول على معرف الخدمة
            cursor.execute("SELECT id FROM services WHERE sercode = %s", (spec_code,))
            service_id = cursor.fetchone()[0]
            
            # إدخال التخصص إذا لم يكن موجوداً
            service_code = str(row[service_code_col]).strip() if not pd.isna(row[service_code_col]) else ''
            cursor.execute("""
                INSERT IGNORE INTO type_specializations (tscode, tsname)
                VALUES (%s, %s)
            """, (
                service_code,
                f"تخصص {service_code}"
            ))
            
            # الحصول على معرف التخصص
            cursor.execute("SELECT id FROM type_specializations WHERE tscode = %s", (service_code,))
            specialization_id = cursor.fetchone()[0]
            
            # إدخال تخصص الخدمة
            cursor.execute("""
                INSERT IGNORE INTO service_specialization 
                (codesv, namesv, price, notes, service_id, type_specialization_id)
                VALUES (%s, %s, %s, %s, %s, %s)
            """, (
                str(row['codesv']).strip(),
                str(row.get('namesv', '')).strip(),
                clean_price(row.get('price', 0)),
                str(row.get('notes', '')).strip(),
                service_id,
                specialization_id
            ))
            
            if index % 100 == 0:  # طباعة التقدم كل 100 سجل
                print(f"تمت معالجة {index + 1} سجلات")
        
        # تأكيد التغييرات
        connection.commit()
        
        # عرض إحصائيات الاستيراد
        cursor.execute("SELECT COUNT(*) FROM services")
        services_count = cursor.fetchone()[0]
        
        cursor.execute("SELECT COUNT(*) FROM type_specializations")
        specializations_count = cursor.fetchone()[0]
        
        cursor.execute("SELECT COUNT(*) FROM service_specialization")
        service_spec_count = cursor.fetchone()[0]
        
        print("\nإحصائيات الاستيراد:")
        print(f"عدد الخدمات: {services_count}")
        print(f"عدد التخصصات: {specializations_count}")
        print(f"عدد تخصصات الخدمات: {service_spec_count}")
        print("\nتم استيراد البيانات بنجاح!")
        
    except Error as e:
        print(f"حدث خطأ: {e}")
        if 'connection' in locals():
            connection.rollback()
    finally:
        if 'connection' in locals() and connection.is_connected():
            cursor.close()
            connection.close()

if __name__ == "__main__":
    import sys
    if len(sys.argv) != 2:
        print("الرجاء تحديد مسار ملف الإكسل")
        print("مثال: python import_service_specialization.py data.xlsx")
        sys.exit(1)
    
    excel_file = sys.argv[1]
    import_excel_to_db(excel_file)
