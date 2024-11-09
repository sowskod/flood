import sys
import pandas as pd
import joblib

# Load the trained model
model = joblib.load('flood_risk_model.pkl')

# Check if sufficient arguments are passed
if len(sys.argv) < 5:
    print("Error: Please provide wind speed, rainfall, temperature, and elevation as arguments.")
    sys.exit(1)  # Exit the script if arguments are missing

# Get input values (wind speed, rainfall, temperature, elevation)
wind_speed = float(sys.argv[1])
rainfall = float(sys.argv[2])
temperature = float(sys.argv[3])
elevation = float(sys.argv[4])

# Elevation and rainfall-based risk handling
if rainfall < 5:
    # If rainfall is below 5mm, all barangays are considered Low Risk
    print("Low Risk")
elif elevation <= 5:
    # If the elevation is less than or equal to 5, the flood risk is always high
    print("High Risk")
elif rainfall >= 50:
    # High risk for all if rainfall is above or equal to 50 mm
    print("High Risk")
elif rainfall < 5 and elevation == 15:
    # Low risk if rainfall is below 5 mm and elevation is 15
    print("Low Risk")
else:
    # For other cases, use the trained model to predict
    input_data = [[wind_speed, rainfall, temperature, elevation]]
    
    # Make prediction using the trained model
    prediction = model.predict(input_data)
    
    # Output the prediction result
    if prediction[0] == 0:
        print("Low Risk")
    elif prediction[0] == 1:
        print("Medium Risk")
    elif prediction[0] == 2:
        print("High Risk")
