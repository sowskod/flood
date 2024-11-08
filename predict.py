import sys
import json
import pandas as pd
from sklearn.ensemble import RandomForestClassifier
import joblib

# Load the trained model
model = joblib.load('flood_prediction_model.joblib')

# Get input data from command line
input_data = json.loads(sys.argv[1])
rainfall = input_data['rainfall']
wind_speed = input_data['wind_speed']

# Create a DataFrame for input data
data = pd.DataFrame([[rainfall, wind_speed]], columns=['rainfall', 'wind_speed'])

# Make prediction
prediction = model.predict(data)

# Format output as required (adjust as per your barangay names and logic)
# Assuming you have a mapping of barangays and their risks
result = {
    'Barangay1': 'High Risk' if prediction[0] == 1 else 'Low Risk',
    'Barangay2': 'Medium Risk' if prediction[0] == 2 else 'No Data',
    # Continue for each barangay...
}

# Print the result as JSON
print(json.dumps(result))
