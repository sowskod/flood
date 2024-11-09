<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flood Prediction System</title>

    <style>
        /* Background styling */
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(90deg, rgba(176, 240, 247, 1) 25%, rgba(114, 230, 207, 1) 66%, rgba(52, 184, 182, 1) 98%);
            font-family: Arial, sans-serif;
            color: #333;
        }

        /* Main container */
        .container {
            text-align: center;
        }

        /* Logo styling */
        .logo {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            margin-top: 20px;
        }

        /* Headings and text */
        h1 {
            font-size: 2em;
            font-weight: bold;
            color: #2D3748;
            /* Dark shade for title */
        }

        p {
            font-size: 1.125em;
            color: #4A5568;
            /* Medium shade for subtitle */
            margin-top: 5px;
        }

        h2 {
            margin-top: 20px;
            font-size: 1.5em;
            font-weight: bold;
            color: #000;
        }

        .button {
        height: 50px;
        width: 300px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transition: all 0.5s ease-in-out;
      }

      .button:hover {
        box-shadow: 0.5px 0.5px 150px #252525;
      }

      .type1::after {
        content: "Thank you!";
        height: 50px;
        width: 300px;
        background-color: #008080;
        color: #fff;
        position: absolute;
        top: 0%;
        left: 0%;
        transform: translateY(50px);
        font-size: 1.2rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.5s ease-in-out;
      }

      .type1::before {
        content: "Click Here to Enter";
        height: 50px;
        width: 300px;
        background-color: light gray;
        color: #008080;
        position: absolute;
        top: 0%;
        left: 0%;
        transform: translateY(0px) scale(1.2);
        font-size: 1.2rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.5s ease-in-out;
      }

      .type1:hover::after {
        transform: translateY(0) scale(1.2);
      }

      .type1:hover::before {
        transform: translateY(-50px) scale(0) rotate(120deg);
      }
    </style>
</head>

<body>
    <div class="container">
        <h1>Flood Prediction System</h1>
        <p>San Rafael, Bulacan</p>
        <h2>WELCOME ADMINISTRATOR!</h2>
        <img class="logo" src="images/san.jpg" alt="Logo">
        <div>
            <br>
        <center>
    <a href="dashboard.php">
        <button class="button type1"></button>
    </a>
</center>

        </div>
    </div>
</body>

</html>