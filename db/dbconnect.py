import mysql.connector

def get_db_connection():
    try:
        conn = mysql.connector.connect(
            host="localhost",
            user="root",
            password="@pokemon1",  # Change this to your actual password
            database="Variant"
        )
        if conn.is_connected():
            print("Database connected successfully!")
            return conn
    except mysql.connector.Error as e:
        print(f"Error connecting to MySQL: {e}")
        return None
