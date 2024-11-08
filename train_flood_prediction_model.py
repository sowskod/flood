import pandas as pd
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import train_test_split
import joblib

# Load your dataset
data = pd.read_csv('flood_data.csv')  # Replace with your dataset

# Assume the dataset has features 'rainfall', 'wind_speed' and a target 'flood_occurred'
X = data[['rainfall', 'wind_speed']]
y = data['flood_occurred']

# Split the data into training and testing sets
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

# Train the Random Forest model
model = RandomForestClassifier(n_estimators=100)
model.fit(X_train, y_train)

# Save the trained model
joblib.dump(model, 'flood_prediction_model.joblib')
