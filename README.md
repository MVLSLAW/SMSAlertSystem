# SMSAlertSystem
Uses the twilio text message service to send text messages to staff. Select 1, a few, or all of the contacts to send a message to.
Messages are logged in SQL.

Empty sql tables are included so you can easily upload them.

##Set Up
- Add you SQL username/password and your Twilio information in sql.php.
- Upload the empty sql database or individual tables.
- Using phpMyAdmin or any mysql interface, add username/password to the credentials table.

##Limitations
<pre>
- Currently there is no way to add login credentials other than directly through SQL.
- All users with credentials have the same rights.
- No way to change or reset password for credentials.
</pre>
