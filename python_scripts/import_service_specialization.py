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
        print(f"service_id: {df['service_id'].nunique()} قيمة فريدة")
        print(f"type_specializations_id: {df['type_specializations_id'].nunique()} قيمة فريدة")
        print(f"codesv: {df['codesv'].nunique()} قيمة فريدة")
        
        # حذف الصفوف التي تحتوي على قيم فارغة في الأعمدة المهمة
        before_rows = len(df)
        df = df.dropna(subset=['service_id', 'type_specializations_id', 'codesv'])
        after_rows = len(df)
        print(f"\nتم حذف {before_rows - after_rows} صفوف فارغة")
        
        # إزالة التكرار
        before_rows = len(df)
        df = df.drop_duplicates(['service_id', 'type_specializations_id', 'codesv'])
        after_rows = len(df)
        print(f"تم حذف {before_rows - after_rows} صفوف مكررة")
        
        # إنشاء قاموس لتحويل service_id القديم إلى الجديد
        unique_services = df['service_id'].unique()
        service_id_map = {old_id: new_id + 1 for new_id, old_id in enumerate(sorted(unique_services))}
        
        # تحديث service_id في DataFrame
        df['service_id'] = df['service_id'].map(service_id_map)
        
        # ترتيب البيانات
        df = df.sort_values(['service_id', 'type_specializations_id'])
        
        print(f"\nعدد الصفوف النهائي للاستيراد: {len(df)}")
        print("\nعينة من البيانات:")
        print(df[['service_id', 'type_specializations_id', 'codesv', 'namesv']].head())
        
        # الاتصال بقاعدة البيانات
        connection = mysql.connector.connect(
            host='localhost',
            user='root',
            password='',
            database='hinpa'
        )
        
        cursor = connection.cursor()

        # حذف جميع السجلات من جدول الخدمات وإعادة ضبط التسلسل
        cursor.execute("TRUNCATE TABLE services")
        
        # إدخال الخدمات مع الترقيم الجديد
        unique_services_df = pd.DataFrame({
            'id': range(1, len(unique_services) + 1),
            'sercode': range(1, len(unique_services) + 1),
            'sername': [f'خدمة {i}' for i in range(1, len(unique_services) + 1)]
        })
        
        for _, row in unique_services_df.iterrows():
            cursor.execute("""
                INSERT INTO services (id, sercode, sername)
                VALUES (%s, %s, %s)
            """, (row['id'], row['sercode'], row['sername']))
        
        connection.commit()
        
        # حذف جميع السجلات وإعادة ضبط التسلسل
        cursor.execute("TRUNCATE TABLE service_specialization")
        
        # تحضير البيانات وإدخالها
        for index, row in df.iterrows():
            # إدخال تخصص الخدمة
            cursor.execute("""
                INSERT INTO service_specialization 
                (codesv, namesv, price, notes, service_id, type_specializations_id)
                VALUES (%s, %s, %s, %s, %s, %s)
            """, (
                str(row['codesv']).strip(),
                str(row.get('namesv', '')).strip(),
                clean_price(row.get('price', 0)),
                str(row.get('notes', '')).strip(),
                int(row['service_id']),
                int(row['type_specializations_id'])
            ))
            
            if index % 100 == 0:
                print(f"تمت معالجة {index + 1} سجلات")
                connection.commit()
        
        connection.commit()
        
        print("\nإحصائيات الاستيراد:")
        cursor.execute("SELECT COUNT(*) FROM services")
        print(f"عدد الخدمات: {cursor.fetchone()[0]}")
        cursor.execute("SELECT COUNT(*) FROM service_specialization")
        print(f"عدد تخصصات الخدمات: {cursor.fetchone()[0]}")
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
        print("مثال: python import_service_specialization.py data.csv")
        sys.exit(1)
    
    excel_file = sys.argv[1]
    import_excel_to_db(excel_file)
