
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New User Registration</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Reset margins and paddings */
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
        }
        .container-all {
            height: 100vh;
            width: 100vw;
            background: #6f9947 ;
            display: flex; /* Add flexbox to center content */
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
        }

        .left-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: auto;
        }

        .right-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: left;
            margin-left: 64px;
            max-width: 450px;
        }

        .bottom-container {
            background-color: #64A651;
            font-size: 16px;
            font-family: 'Roboto', sans-serif;
            font-weight: bold;
            border-radius: 10px;
            color: #ffffff;
            padding-left: 16px;
            padding-top: 1px;
            padding-bottom: 1px;
            margin-right: 400px;
            margin-left: 400px;
            position:fixed;
            bottom: 0;
            display: none;
            z-index: 1000;
        }

        .name {
            color: #131313;
            max-width: 200px;
        }
        .role{
            width: 200px;
        }
        .department {
            color: #131313;
            max-width: 200px;
        }

        .pin {
            color: #131313;
            max-width: 200px;
        }

        button {
            padding: 10px 20px;
            margin-top: 20px;
            font-size: 15px;
            cursor: pointer;
        }

        #capture-button {
            background-color: #64A651;
            color: #131313;
            border: none;
            border-radius: 4px;
            
            margin-top: 10px;
            font-size: 40px;
            font-family: 'Roboto', sans-serif;
            font-weight: bold;
        }

        #back-button {
            background-color: #FF9933;
            color:  #131313;
            border: none;
            border-radius: 4px;
            margin-top: 10px;
            
            font-size: 40px;
            font-family: 'Roboto', sans-serif;
            font-weight: bold;
        }

        .name_div{
            width: 50%; /* Set the width to half of the right-container */
            
            margin-right: 25px;
        }
        .dept_div{
            width: 50%; /* Set the width to half of the right-container */
            margin-right: 25px;
        }
        
        input, select {
            font-size: 24px;
            font-family: 'Roboto', sans-serif;
            font-weight: bold;
            padding: 10px;
            background-color: transparent; /* Removes background */
            border: none; /* Removes all borders */
            border-bottom: 2px solid #000; /* Adds a bottom border */
            outline: none; /* Removes the default focus outline */
        }
        
        select:hover {
            cursor: pointer;
        }

        input::placeholder {
            color: #131313; /* Change this to your desired color */
        }

        #capture-button{
            background-color: #2589BD;
            font-size: 32px;
            font-family: 'Roboto', sans-serif;
            font-weight: bold;
            border: 5px solid #131313;
            border-radius: 10px;
        }
        #capture-button:hover {
            background-color: #227BAA;
            cursor: pointer;
        }

        #back-button {
            background-color: #FCA61D;
            font-size: 32px;
            font-family: 'Roboto', sans-serif;
            font-weight: bold;
            border: 5px solid #131313;
            border-radius: 10px;
        }
        #back-button:hover {
            background-color: #FFCC66;
            cursor: pointer;
        }
        #camera-select {
            font-size: 15px;
            font-family: 'Roboto', sans-serif;
            font-weight: bold;
            padding: 5px;
            background-color: transparent;
            border: none;
            outline: none;
        }
        video {
            border: 3px solid #131313;
            border-radius: 5px;
            width: 300px;
            height: 300px;
            object-fit: cover;
        }

        .row_1{
            display: flex;
            align-items: flex-end;
            
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container-all">
        <div class="left-container">

        <!-- Dropdown selector if there are multiple camera sources -->
        <select id="camera-select" style="display:none;">
            <option value="" disabled selected>Select Camera</option>
        </select>

        <!-- Video element to display webcam stream -->
        <video id="webcam" autoplay></video>

        <!-- Canvas element to capture and draw image -->
        <canvas id="canvas" style="display:none;"></canvas>

        </div>
        <div class="right-container">            
            <div class="row_1">
                <div class="name_div" >
                    <input type="text" id="name" class="name" placeholder="Name" required>
                </div>
                <div>
                    <select id="role" class="role" required>
                        <option value="" disabled selected>Position</option>
                        <option value="Admin">Admin</option>
                        <option value="Manager">Manager</option>
                        <option value="Employee">Employee</option>
                    </select>
                </div>
            </div>
            <br>
            <div class="row_1">
                <div>
                    
                <select id="department" class="department" required>
                        <option value="" disabled selected>Department</option>
                        <option value="sales">Sales</option>
                        <option value="purchasing">Purchasing</option>
                        <option value="proddev">Product Development</option>
                        <option value="warehouse">Warehouse</option>
                        <option value="logistics">Logistics</option>
                        <option value="accounting">Accounting</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>
            <br>
            <button id="capture-button">Submit</button>
            <button id="back-button">Back</button>
        </div>

        <div class="bottom-container">

        <!-- Text that changes to show the user the registration status -->
        <p id="status">Registration status: Waiting for capture...</p>

        </div>
    </div>

    <!-- JavaScript code to capture the image and send it to the Python script -->
    <script>

        // Function to populate the camera dropdown selector
        function populateCameraOptions() {
            navigator.mediaDevices.enumerateDevices()
                .then((devices) => {
                    const videoDevices = devices.filter(device => device.kind === 'videoinput');
                    const cameraSelect = document.getElementById('camera-select');
                    cameraSelect.style.display = 'block'; // Show the dropdown

                    videoDevices.forEach((device, index) => {
                        const option = document.createElement('option');
                        option.value = device.deviceId;
                        option.text = device.label || `Camera ${index + 1}`;
                        cameraSelect.appendChild(option);
                    });

                    // Set the first camera as the default selected option
                    if (videoDevices.length > -1) {
                        cameraSelect.value = videoDevices[0].deviceId;
                        startWebcam(videoDevices[0].deviceId);
                    }

                    // Change the webcam source when a different camera is selected
                    cameraSelect.addEventListener('change', (event) => {
                        startWebcam(event.target.value);
                    });
                })
                .catch((error) => {
                    console.error("Error enumerating devices: ", error);
                });
        }

        // Function to start the webcam with the selected device ID
        function startWebcam(deviceId) {
            navigator.mediaDevices.getUserMedia({ video: { deviceId: { exact: deviceId } } })
                .then((stream) => {
                    const webcamElement = document.getElementById('webcam');
                    webcamElement.srcObject = stream;
                })
                .catch((error) => {
                    console.error("Error accessing webcam: ", error);
                });
        }

        // Populate camera options on page load
        populateCameraOptions();

        // Return to the welcome page when the back button is clicked
        document.getElementById('back-button').addEventListener('click', function() {
            window.location.href = '../../welcome.php';
        });
        
        // Get elements from the DOM
        const webcamElement = document.getElementById('webcam');
        const canvasElement = document.getElementById('canvas');
        const captureButton = document.getElementById('capture-button');
        const canvasContext = canvasElement.getContext('2d');

        // Initialize webcam stream
        function initWebcam() {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then((stream) => {
                    webcamElement.srcObject = stream;
                })
                .catch((error) => {
                    console.error("Error accessing webcam: ", error);
                });
        }

        // Capture image function
        function captureImage() {
            // Set canvas width and height to video element's width and height
            canvasElement.width = webcamElement.videoWidth;
            canvasElement.height = webcamElement.videoHeight;

            // Draw the current frame from the video to the canvas
            canvasContext.drawImage(webcamElement, 0, 0, canvasElement.width, canvasElement.height);
            
            // Convert the canvas to a base64-encoded PNG image
            const image = canvasElement.toDataURL('image/png');

            const name = document.getElementById('name').value;
            const role = document.getElementById('role').value;
            const department = document.getElementById('department').value;
            
            // Check if name is at least min of 7 max of 50 characters  
            if (name.length < 7 || name.length > 50) {
                document.getElementById('status').textContent = 'Name must be between 7 and 50 characters';
                return;
            }
            
            // Prepare the data payload to send to the Python script
            const dataPayload = { 
                image: image.split(',')[1], 
                name: name,
                role: role,
                department: department,
            };

            
            
            // display the payload
            console.log(dataPayload);

            // Send the image data to the Python script
            // https://6dvfd2bd-5000.asse.devtunnels.ms/
            fetch('https://6dvfd2bd-5000.asse.devtunnels.ms/Face_API/register', {  // Adjust the URL to your Python script's path
                method: 'POST',
                body: JSON.stringify(dataPayload),
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then((response) => response.json()) // Parse the JSON response
            .then((data) => { 
                console.log("Server response:", data); // Log the server response to the console 
                if (data.success) {
                    document.getElementById('status').textContent = data.message;

                    
                    document.querySelector('.bottom-container').style.backgroundColor = '#64A651';
                    
                    // make bottom container visible
                    document.querySelector('.bottom-container').style.display = 'block';

                    // Redirect to index.php after successful registration
                    setTimeout(() => { // Delay the redirect for 1 second
                        window.location.href = '/?page=login';
                    }, 2500);
                } else {
                    document.getElementById('status').textContent = data.message;
                    // make bottom container visible
                    document.querySelector('.bottom-container').style.display = 'block';
                    // make bottom container background color red
                    document.querySelector('.bottom-container').style.backgroundColor = '#FF4C4C';

                    // dissapear the bottom container after 3 seconds
                    setTimeout(() => {
                        document.querySelector('.bottom-container').style.display = 'none';
                    }, 2500);
                }

            })
            .catch((error) => {
                console.error("Error sending image to server: ", error);
            });
        }

        // Initialize webcam on page load
        initWebcam();

        // Capture image when button is clicked
        captureButton.addEventListener('click', captureImage);
    </script>
</body>
</html>