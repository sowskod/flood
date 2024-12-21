import pandas as pd
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import train_test_split, GridSearchCV
import joblib
from sklearn.preprocessing import StandardScaler
from sklearn.metrics import (
    accuracy_score,
    precision_score,
    recall_score,
    f1_score,
    confusion_matrix,
    classification_report,
    mean_absolute_error,
    mean_squared_error,
    explained_variance_score,
    r2_score  # Import R² score
)
from sklearn.model_selection import cross_val_score
import numpy as np

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

# Make predictions on the test set
y_pred = best_model.predict(X_test)

# Classification metrics
print("Classification Metrics:")
print(f"Accuracy: {accuracy_score(y_test, y_pred):.2f}")
print(f"Precision (macro): {precision_score(y_test, y_pred, average='macro'):.2f}")
print(f"Recall (macro): {recall_score(y_test, y_pred, average='macro'):.2f}")
print(f"F1 Score (macro): {f1_score(y_test, y_pred, average='macro'):.2f}")
print("Confusion Matrix:")
print(confusion_matrix(y_test, y_pred))
print("\nClassification Report:")
print(classification_report(y_test, y_pred))

# Numeric evaluation metrics
y_pred_numeric = best_model.predict(X_test)
print("\nNumeric Evaluation Metrics:")
print(f"Mean Absolute Error (MAE): {mean_absolute_error(y_test, y_pred_numeric):.2f}")
print(f"Mean Squared Error (MSE): {mean_squared_error(y_test, y_pred_numeric):.2f}")
print(f"Root Mean Squared Error (RMSE): {np.sqrt(mean_squared_error(y_test, y_pred_numeric)):.2f}")
print(f"Explained Variance Score (EVS): {explained_variance_score(y_test, y_pred_numeric):.2f}")
print(f"R² Score: {r2_score(y_test, y_pred_numeric):.2f}")

# Save the trained model
joblib.dump(best_model, 'flood_risk_model.pkl')
