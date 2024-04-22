from flask import Flask, render_template, request, redirect, url_for
from flask_wtf import FlaskForm
from wtforms import StringField, TextAreaField, SubmitButton
from wtforms.validators import DataRequired, Email
from flask_mail import Mail, Message

app = Flask(__name__)

# SMTP Configuration
app.config['MAIL_SERVER'] = 'smtp.slonjohswe.tech'
app.config['MAIL_PORT'] = 587
app.config['MAIL_USE_TLS'] = True
app.config['MAIL_USERNAME'] = 'mail@slonjohswe.tech'
app.config['MAIL_PASSWORD'] = 'kqQz@!)9'

mail = Mail(app)

# Define a Flask-WTF form
class ContactForm(FlaskForm):
    name = StringField('Your Name', validators=[DataRequired()])
    email = StringField('Your Email', validators=[DataRequired(), Email()])
    subject = StringField('Subject', validators=[DataRequired()])
    message = TextAreaField('Message', validators=[DataRequired()])
    submit = SubmitButton('Send Message')

@app.route('/contact', methods=['GET', 'POST'])
def contact():
    form = ContactForm()
    if form.validate_on_submit():
        # Create and send an email
        msg = Message(form.subject.data, sender=(form.name.data, form.email.data), recipients=['besttec1997@gmail.com'])
        msg.body = form.message.data
        mail.send(msg)
        return redirect(url_for('contact'))  # Redirect to avoid form resubmission

    return render_template('contact.html', form=form)  # Use a template to render the form

if __name__ == '__main__':
    app.run(debug=True)  # Run the Flask server
