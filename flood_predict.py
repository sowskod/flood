import sys
import pandas as pd
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import train_test_split
from sklearn.metrics import classification_report

# Sample dataset
data = {
    'Brgy Name': ['Banca-Banca', 'BMA – Balagtas', 'Caingin', 'Capihan', 'Coral na Bato', 'Cruz na Daan', 'Dagat-Dagatan',
                  'Diliman I', 'Diliman II', 'Libis', 'Lico', 'Maasim', 'Mabalas-Balas', 'Maguinao', 'Maronquillo', 'Paco',
                  'Pansumaloc', 'Pantubig', 'Pasong Bangkal', 'Pasong Callos', 'Pasong Intsik', 'Pinacpinacan', 'Poblacion', 
                  'Pulo', 'Pulong Bayabas', 'Salapungan', 'Sampaloc', 'San Agustin', 'San Roque', 'Sapang Pahalang', 'Talacsan',
                  'Tambubong', 'Tukod', 'Ulingao'],
    'Rainfall (mm)': [50, 30, 100, 70, 20, 110, 45, 90, 150, 20, 50, 200, 30, 60, 40, 110, 170, 10, 30, 40, 20, 90, 50, 25, 
                      60, 80, 90, 10, 130, 40, 50, 180, 20, 100],
    'Wind Speed (km/h)': [40, 20, 50, 60, 10, 70, 30, 80, 100, 10, 40, 120, 20, 40, 30, 80, 110, 20, 20, 25, 15, 60, 35, 
                           15, 50, 65, 70, 20, 95, 25, 45, 110, 15, 75],
    'Temperature (°C)': [30, 28, 32, 29, 30, 33, 28, 31, 34, 27, 29, 30, 26, 29, 28, 32, 33, 28, 26, 27, 30, 32, 29, 30, 
                          31, 32, 33, 28, 34, 30, 29, 35, 30, 31],
    'Risk Level': ['Low', 'Low', 'Medium', 'Low', 'Low', 'Medium', 'Low', 'Medium', 'High', 'Low', 'Low', 'High', 'Low', 'Low',
                   'Low', 'Medium', 'High', 'Low', 'Low', 'Low', 'Low', 'Medium', 'Low', 'Low', 'Low', 'Low', 'Low', 'Medium', 
                   'Low', 'Low', 'High', 'Low', 'Medium']
}

# Create a dataframe from the dictionary
df = pd.DataFrame(data)

# Map categorical target to numerical values
df['Risk Level'] = df['Risk Level'].map({'Low': 0, 'Medium': 1, 'High': 2})

# Feature and target variables
X = df[['Rainfall (mm)', 'Wind Speed (km/h)', 'Temperature (°C)']]
y = df['Risk Level']

# Split the dataset into training and testing sets
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

# Initialize RandomForestClassifier
model = RandomForestClassifier(n_estimators=100, random_state=42)

# Train the model
model.fit(X_train, y_train)

# Predict risk level for the given inputs
def predict_risk(rainfall, wind_speed, temperature):
    prediction = model.predict([[rainfall, wind_speed, temperature]])
    risk_mapping = {0: 'Low', 1: 'Medium', 2: 'High'}
    return risk_mapping[prediction[0]]

# Get the input arguments from the command line
brgy_name = sys.argv[1]  # Barangay name, for reference or logging
rainfall = float(sys.argv[2])  # Rainfall in mm
wind_speed = float(sys.argv[3])  # Wind speed in km/h
temperature = float(sys.argv[4])  # Temperature in °C

# Get the predicted flood risk level
flood_risk = predict_risk(rainfall, wind_speed, temperature)

# Output the result
print(flood_risk)
