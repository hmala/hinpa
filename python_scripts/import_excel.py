import pandas as pd
import pymysql
import os
from dotenv import load_dotenv

# تحميل متغيرات البيئة من ملف .env
load_dotenv()

# إعدادات الاتصال بقاعدة البيانات
db_config = {
    'host': os.getenv('DB_HOST', '127.0.0.1'),
    'user': os.getenv('DB_USERNAME', 'root'),
    'password': os.getenv('DB_PASSWORD', ''),
    'database': os.getenv('DB_DATABASE', 'hinpa'),
    'port': int(os.getenv('DB_PORT', 3306)),
    'charset': 'utf8mb4'
}

def connect_to_database():
    """الاتصال بقاعدة البيانات"""
    try:
        connection = pymysql.connect(**db_config)
        print("تم الاتصال بقاعدة البيانات بنجاح!")
        return connection
    except Exception as e:
        print(f"خطأ في الاتصال بقاعدة البيانات: {e}")
        return None

def import_excel_to_database(excel_file, table_name, columns_mapping=None):
    """
    استيراد بيانات من ملف Excel إلى قاعدة البيانات
    
    :param excel_file: مسار ملف Excel
    :param table_name: اسم الجدول في قاعدة البيانات
    :param columns_mapping: قاموس يحتوي على تخطيط الأعمدة (Excel column -> DB column)
    """
    try:
        # قراءة ملف Excel
        df = pd.read_excel(excel_file)
        
        # تطبيق تخطيط الأعمدة إذا تم توفيره
        if columns_mapping:
            df = df.rename(columns=columns_mapping)
        
        # الاتصال بقاعدة البيانات
        connection = connect_to_database()
        if not connection:
            return
        
        cursor = connection.cursor()
        
        # تحويل DataFrame إلى قائمة من السجلات
        records = df.to_dict('records')
        
        # إعداد استعلام الإدخال
        columns = ', '.join(records[0].keys())
        values = ', '.join(['%s'] * len(records[0]))
        query = f"INSERT INTO {table_name} ({columns}) VALUES ({values})"
        
        # إدخال البيانات
        for record in records:
            cursor.execute(query, tuple(record.values()))
        
        # حفظ التغييرات
        connection.commit()
        print(f"تم استيراد {len(records)} سجل بنجاح إلى جدول {table_name}!")
        
    except Exception as e:
        print(f"حدث خطأ: {e}")
        if 'connection' in locals() and connection:
            connection.rollback()
    
    finally:
        if 'connection' in locals() and connection:
            connection.close()

def main():
    """الدالة الرئيسية"""
    print("برنامج استيراد البيانات من Excel إلى قاعدة البيانات")
    
    while True:
        print("\nالخيارات المتاحة:")
        print("1. استيراد ملف Excel")
        print("2. خروج")
        
        choice = input("\nاختر رقم العملية: ")
        
        if choice == "2":
            print("شكراً لاستخدام البرنامج!")
            break
            
        elif choice == "1":
            excel_file = input("أدخل مسار ملف Excel: ")
            table_name = input("أدخل اسم الجدول في قاعدة البيانات: ")
            
            # سؤال المستخدم عن تخطيط الأعمدة
            mapping_needed = input("هل تريد تعيين أسماء الأعمدة؟ (نعم/لا): ").lower()
            columns_mapping = None
            
            if mapping_needed == "نعم":
                columns_mapping = {}
                print("أدخل تخطيط الأعمدة (اضغط Enter بدون إدخال للانتهاء):")
                while True:
                    excel_col = input("اسم العمود في Excel: ")
                    if not excel_col:
                        break
                    db_col = input("اسم العمود في قاعدة البيانات: ")
                    columns_mapping[excel_col] = db_col
            
            # تنفيذ عملية الاستيراد
            import_excel_to_database(excel_file, table_name, columns_mapping)

if __name__ == "__main__":
    main()
