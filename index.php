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
      .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
            z-index: 10;
            flex-direction: column;
            padding: 20px;
        }

        .overlay h2 {
            font-size: 6em;
            color: maroon;
            text-shadow: 4px 4px 5px red;
            margin-bottom: 10px;
        }

        .overlay h4 {
            font-size: 1.25em;
            text-shadow: 4px 4px 5px red;
            margin-bottom: 20px;
        }

        /* About Developer button styling */
        .about-button {
            padding: 10px 20px;
            background: rgb(210,81,81);
background: linear-gradient(90deg, rgba(210,81,81,1) 20%, rgba(226,39,39,1) 56%, rgba(217,0,0,1) 80%);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
        }

        /* Modal styling */
        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 20;
            max-width: 300px;
            text-align: center;
        }

        .modal h2 {
            margin: 0 0 10px;
        }

        .modal p {
            margin: 0;
            color: #333;
        }

        .modal-close {
            margin-top: 15px;
            background-color: #008080;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }

    

    </style>
</head>

<body>
<!--<div class="overlay">
        <h2>Website Restricted by Developer Due to Incomplete Payment for Development.</h2>
        <h4>If you are the site owner, please contact the developer and make the payment before your final defense, or your website will be lost.</h4>
        <button class="about-button" onclick="showModal()">About Payment</button>
    </div>
    
        /* Main container */
        .container {
            text-align: center;
            opacity: 0.99; /* Dim the main content */
            pointer-events: none; /* Disable interaction */
        }-->
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

   <!-- <div class="modal" id="developerModal">
        <h2>Client Information</h2>
        <p>Name: Eljohn Tristan Del Rosario</p><br>
      <p>For sale: Kambing, Tricycle, </p>
      <p>Panabong na Manok sama na si Shiro</p>
      <p>discounted pag package</p>
      <p>PM For Price</p>
        
        <button class="modal-close" onclick="hideModal()">Close</button>
    </div>

    <script>
        // Show the modal
        function showModal() {
            document.getElementById('developerModal').style.display = 'block';
        }

        // Hide the modal
        function hideModal() {
            document.getElementById('developerModal').style.display = 'none';
        }
    </script>-->
</body>

</html>