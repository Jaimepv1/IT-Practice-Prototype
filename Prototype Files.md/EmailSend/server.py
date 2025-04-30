from flask import Flask, request, redirect, send_from_directory
import smtplib
from email.message import EmailMessage
from pymongo import MongoClient

app = Flask(__name__)

# MongoDB setup
client = MongoClient('mongodb://localhost:27017/')
db = client['email_automation']
emails_collection = db['sent_emails']

# Gmail credentials — replace with your actual Gmail address and app password
EMAIL_ADDRESS = 'yahyeaidid@gmail.com'
EMAIL_PASSWORD = 'pxtixjpmolfjfwni'  # your Gmail App Password here

# Route to serve the Email.html file
@app.route('/')
def home():
    return send_from_directory('.', 'Email.html')

# Route to handle email sending
@app.route('/send_email', methods=['POST'])
def send_email():
    recipient = request.form['recipient_email']
    subject = request.form['subject']
    message_body = request.form['message']

    msg = EmailMessage()
    msg['Subject'] = subject
    msg['From'] = EMAIL_ADDRESS
    msg['To'] = recipient
    msg.set_content(message_body)

    try:
        # Connect and send email through Gmail's SMTP server
        with smtplib.SMTP_SSL('smtp.gmail.com', 465) as smtp:
            smtp.login(EMAIL_ADDRESS, EMAIL_PASSWORD)
            smtp.send_message(msg)

        # Log sent email to MongoDB
        emails_collection.insert_one({
            'recipient': recipient,
            'subject': subject,
            'message': message_body
        })

        print(f'✅ Email sent successfully to {recipient}')

    except Exception as e:
        print(f'❌ Failed to send email: {e}')

    return redirect('/')

if __name__ == '__main__':
    app.run(debug=True)
