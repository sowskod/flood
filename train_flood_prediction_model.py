import pandas as pd
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import train_test_split, GridSearchCV
import joblib
from sklearn.preprocessing import StandardScaler
from sklearn.model_selection import cross_val_score

# Load your CSV data
data = pd.read_csv("barangay_flood_risk.csv")

# Features (independent variables), now including 'Elevation'
X = data[['Rainfall (mm)', 'Wind Speed (km/h)', 'Temperature (°C)', 'Elevation']]

# Labels (dependent variable)
y = data['Risk Level'].map({'Low': 0, 'Medium': 1, 'High': 2})  # Mapping 'Low', 'Medium', 'High' to 0, 1, 2

# Split data into training and testing sets
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.3, random_state=42)

# Standardize the features
scaler = StandardScaler()
X_train = scaler.fit_transform(X_train)
X_test = scaler.transform(X_test)

# Define parameter grid for hyperparameter tuning
param_grid = {
    'n_estimators': [100, 200, 300],
    'max_depth': [10, 20, 30, None],
    'min_samples_split': [2, 5, 10],
    'min_samples_leaf': [1, 2, 4],
    'bootstrap': [True, False],
    'class_weight': ['balanced']
}

# Initialize the model
rf = RandomForestClassifier(random_state=42)

# Perform grid search for hyperparameter tuning
grid_search = GridSearchCV(estimator=rf, param_grid=param_grid, cv=5, n_jobs=-1, scoring='accuracy')
grid_search.fit(X_train, y_train)

# Retrieve the best model
best_model = grid_search.best_estimator_

# Evaluate the best model using cross-validation
scores = cross_val_score(best_model, X, y, cv=5, scoring='accuracy')
print(f'Cross-validated accuracy: {scores.mean():.2f}')

# Retrain the best model on the entire training set
best_model.fit(X_train, y_train)

# Save the trained model
joblib.dump(best_model, 'flood_risk_model.pkl')
