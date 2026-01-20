import pandas as pd
import mysql.connector
from mysql.connector import Error
import sys

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

def verify_data(connection, df, spec_code_col, service_code_col):
    cursor = connection.cursor()
    issues = []
    
    for index, row in df.iterrows():
        spec_code = str(row[spec_code_col]).strip() if not pd.isna(row[spec_code_col]) else ''
        service_code = str(row[service_code_col]).strip() if not pd.isna(row[service_code_col]) else ''
        codesv = str(row['codesv']).strip()
        
        if not spec_code:
            issues.append(f"صف {index + 1}: كود التخصص فارغ")
        if not service_code:
            issues.append(f"صف {index + 1}: كود الخدمة فارغ")
        if not codesv:
            issues.append(f"صف {index + 1}: كود تخصص الخدمة فارغ")
            
    return issues

def import_excel_to_db(excel_file):
    try:
        # قراءة ملف CSV وتنظيف أسماء الأعمدة
        df = pd.read_csv(excel_file, encoding='utf-8')
        print(f"\nإحصائيات الملف قبل المعالجة:")
        print(f"عدد الصفوف الكلي: {len(df)}")
        print(f"عدد الأعمدة: {len(df.columns)}")
        
        # تنظيف أسماء الأعمدة
        df.columns = df.columns.str.strip()
        spec_code_col = 'specialization_code\xa0'.strip()
        service_code_col = 'service_code\xa0'.strip()
        
        # عرض توزيع البيانات
        print("\nتوزيع القيم الفريدة:")
        print(f"{spec_code_col}: {df[spec_code_col].nunique()} قيمة فريدة")
        print(f"{service_code_col}: {df[service_code_col].nunique()} قيمة فريدة")
        print(f"codesv: {df['codesv'].nunique()} قيمة فريدة")
        
        # حذف الصفوف التي تحتوي على قيم فارغة في الأعمدة المهمة
        before_rows = len(df)
        df = df.dropna(subset=[spec_code_col, service_code_col, 'codesv'])
        after_rows = len(df)
        print(f"\nتم حذف {before_rows - after_rows} صفوف فارغة")
        
        # الاتصال بقاعدة البيانات
        connection = mysql.connector.connect(
            host='localhost',
            user='root',
            password='',
            database='hinpa'
        )
        
        # التحقق من البيانات قبل الإدخال
        issues = verify_data(connection, df, spec_code_col, service_code_col)
        if issues:
            print("\nتم العثور على مشاكل في البيانات:")
            for issue in issues:
                print(issue)
            return
        
        cursor = connection.cursor()
        
        # تتبع الإدخالات
        successful_inserts = 0
        failed_inserts = 0
        error_rows = []
        
        # تحضير البيانات وإدخالها
        for index, row in df.iterrows():
            try:
                # إدخال الخدمة
                spec_code = str(row[spec_code_col]).strip() if not pd.isna(row[spec_code_col]) else ''
                cursor.execute("""
                    INSERT IGNORE INTO services (sercode, sername)
                    VALUES (%s, %s)
                """, (spec_code, f"خدمة {spec_code}"))
                
                # الحصول على معرف الخدمة
                cursor.execute("SELECT id FROM services WHERE sercode = %s", (spec_code,))
                service_id = cursor.fetchone()[0]
                
                # إدخال التخصص
                service_code = str(row[service_code_col]).strip() if not pd.isna(row[service_code_col]) else ''
                cursor.execute("""
                    INSERT IGNORE INTO type_specializations (tscode, tsname)
                    VALUES (%s, %s)
                """, (service_code, f"تخصص {service_code}"))
                
                # الحصول على معرف التخصص
                cursor.execute("SELECT id FROM type_specializations WHERE tscode = %s", (service_code,))
                specialization_id = cursor.fetchone()[0]
                
                # إدخال تخصص الخدمة
                cursor.execute("""
                    INSERT INTO service_specialization 
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
                
                successful_inserts += 1
                
                if index % 100 == 0:  # طباعة التقدم كل 100 سجل
                    print(f"تمت معالجة {index + 1} سجلات")
                    connection.commit()  # حفظ التغييرات كل 100 سجل
                    
            except Error as e:
                failed_inserts += 1
                error_rows.append((index + 1, str(e)))
                print(f"\nخطأ في السجل {index + 1}: {e}")
                continue
        
        # تأكيد التغييرات النهائية
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
        print(f"\nالسجلات الناجحة: {successful_inserts}")
        print(f"السجلات الفاشلة: {failed_inserts}")
        
        if error_rows:
            print("\nتفاصيل الأخطاء:")
            for row_num, error in error_rows:
                print(f"صف {row_num}: {error}")
                
    except Error as e:
        print(f"حدث خطأ في قاعدة البيانات: {e}")
        if 'connection' in locals():
            connection.rollback()
    except Exception as e:
        print(f"حدث خطأ غير متوقع: {e}")
    finally:
        if 'connection' in locals() and connection.is_connected():
            cursor.close()
            connection.close()

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("الرجاء تحديد مسار ملف CSV")
        print("مثال: python import_service_specialization_updated.py data.csv")
        sys.exit(1)
    
    excel_file = sys.argv[1]
    import_excel_to_db(excel_file)