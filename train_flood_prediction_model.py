import pandas as pd
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import train_test_split
import joblib

# Load your CSV data
data = pd.read_csv("barangay_flood_risk.csv")

# Features (independent variables)
X = data[['Rainfall (mm)', 'Wind Speed (km/h)', 'Temperature (°C)', 'Elevation (m)']]

# Labels (dependent variable)
y = data['Risk Level'].map({'Low': 0, 'Medium': 1, 'High': 2})  # Mapping 'Low', 'Medium', 'High' to 0, 1, 2

# Split data into training and testing sets
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.3, random_state=42)

# Initialize and train the Random Forest model
model = RandomForestClassifier(n_estimators=100, random_state=42)
model.fit(X_train, y_train)

# Save the model to a file
joblib.dump(model, 'flood_risk_model.pkl')
