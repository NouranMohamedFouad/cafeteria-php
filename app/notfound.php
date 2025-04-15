<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>404 Page Not Found</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body, html {
      height: 100%;
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .bg-container {
      background-image: url('../assets/notfound.jpg');
      background-size: cover;
      background-position: center;
      height: 100%;
      position: relative;
    }

    .overlay {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(5px);
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 20px;
    }

    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: white;
      font-size: 0.9rem;
    }

    .top-bar .left {
      font-size: 2rem;
      font-weight: bold;
    }

    .top-bar .center {
      text-align: center;
      flex-grow: 1;
    }

    .center-box {
      text-align: center;
      color: white;
      margin-top: 10vh;
      background-color: rgba(0, 0, 0, 0.4); 
      padding: 30px;                        
      border-radius: 20px;  
    }

    .center-box h1 {
      font-size: 5rem;
      font-weight: 200;
    }

    .center-box h2 {
      font-size: 2rem;
      font-weight: 400;
      margin-bottom: 20px;
    }

    .center-box p {
      font-size: 1rem;
      color: #eee;
      max-width: 600px;
      margin: auto;
    }

    .center-box .btn-outline-light {
      border: 1px solid #fff;
      margin-top: 30px;
      padding: 10px 30px;
      text-transform: uppercase;
      font-size: 0.9rem;
      letter-spacing: 1px;
    }

    .btn-outline-light:hover {
      background-color: white;
      color: #000;
    }
  </style>
</head>
<body>

  <div class="bg-container">
    <div class="overlay">

      <div class="center-box">
        <h1>404</h1>
        <h2>Page Not Found</h2>
        <p>Oops! The page you are looking for does not exist. It might have been moved or deleted.</p>
        <a href="home.php" class="btn btn-outline-light mt-3">Back to Home</a>
      </div>
    </div>
  </div>

</body>
</html>
