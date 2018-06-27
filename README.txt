Sends readings from Fermentrack[http://www.fermentrack.com/] into an AWS dynamo table.

1. Create a DynamoDB with a Numeric primary key 'ts'

2. Set the following environment variables:
DYNAMODB_TABLE - the name of the DynamoDB table you just created
AWS_REGION - the name of the AWS region the DynamoDB table is installed in (eg eu-west-1)
FERMENTRACK_URL - the base URL of your Fermentrack installation (eg http://192.168.0.111)
AWS_ACCESS_KEY_ID - the access key ID of an IAM user with permissions to write to the DynamoDB table
AWS_SECRET_ACCESS_KEY - the access key of an IAM user with permissions to write to the DynamoDB table

3. Run php ./send_brewery_readings.php
