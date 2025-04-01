<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Energy Data Dashboard</title>
  <style>
    /* Reset defaults */
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #e0eafc, #cfdef3);
      color: #333;
      padding: 30px;
      min-height: 100vh;
    }
    h1 {
      text-align: center;
      color: #2e7d32;
      margin-bottom: 30px;
      text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
    }
    .data-container {
      max-width: 1100px;
      margin: 0 auto;
      background-color: #fff;
      border-radius: 12px;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
      padding: 40px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .data-container:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
    }
    /* Grid layout for boxes */
    .data-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 30px;
    }
    .data-box {
      background-color: #f9f9f9;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 3px 6px rgba(0, 0, 0, 0.08);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .data-box:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }
    /* Make the Energy to Sell box larger */
    .large-box {
      grid-column: span 2;
    }
    .data-label {
      font-weight: 600;
      color: #555;
      margin-bottom: 10px;
    }
    .data-value {
      font-weight: 700;
      font-size: 1.2rem;
      color: #2e7d32;
    }
    .data-value-negative {
      color: #d32f2f;
    }
    p {
      text-align: center;
      color: #888;
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <h1>Energy Data Dashboard</h1>
  <div class="data-container" id="data-container">
    <p>Loading data...</p>
  </div>

  <script>
    let data = [];
    let currentIndex = 0;
    // Get current month name (e.g., "August")
    const currentMonthName = new Date().toLocaleString('default', { month: 'long' });

    // Fetch all data from the server
    function fetchEnergyData() {
      fetch('fetch_data.php')
        .then(response => response.json())
        .then(responseData => {
          if (responseData.error) {
            document.getElementById('data-container').innerHTML = `<p>Error: ${responseData.error}</p>`;
          } else {
            data = responseData;
            currentIndex = 0;
            displayCurrentData();
          }
        })
        .catch(error => {
          console.error('Error fetching data:', error);
          document.getElementById('data-container').innerHTML = `<p>Error fetching data.</p>`;
        });
    }

    // Determine the appropriate class based on value
    function getValueClass(value) {
      return value >= 0 ? 'data-value' : 'data-value-negative';
    }

    // Display the current row of data in grid boxes
    function displayCurrentData() {
      if (data.length > 0) {
        const row = data[currentIndex];
        const energyToSellContent = row.energy_to_sell >= 0 
          ? `${row.energy_to_sell} kWh`
          : `N/A`;

        document.getElementById('data-container').innerHTML = `
          <div class="data-grid">
            <div class="data-box">
              <div class="data-label">Date & Time</div>
              <div class="data-value">${row.datetime}</div>
            </div>
            <div class="data-box">
              <div class="data-label">Generation (${currentMonthName})</div>
              <div class="data-value">${row.generation_month1} kWh</div>
            </div>
            <div class="data-box">
              <div class="data-label">Predicted Generation</div>
              <div class="data-value">${row.predicted_generation} kWh</div>
            </div>
            <div class="data-box">
              <div class="data-label">Consumption (${currentMonthName})</div>
              <div class="data-value">${row.consumption_month1} kWh</div>
            </div>
            <div class="data-box">
              <div class="data-label">Predicted Consumption</div>
              <div class="data-value">${row.predicted_consumption} kWh</div>
            </div>
            <div class="data-box">
              <div class="data-label">Surplus Energy (${currentMonthName})</div>
              <div class="data-value">${row.surplus_energy_month1} kWh</div>
            </div>
            <div class="data-box">
              <div class="data-label">Predicted Surplus Energy</div>
              <div class="data-value">${row.predicted_surplus_energy} kWh</div>
            </div>
            <div class="data-box large-box">
              <div class="data-label">Energy to Sell</div>
              <div class="${getValueClass(row.energy_to_sell)}">${energyToSellContent}</div>
            </div>
          </div>
        `;
    
        // Cycle to the next row
        currentIndex = (currentIndex + 1) % data.length;
      } else {
        document.getElementById('data-container').innerHTML = `<p>No data available.</p>`;
      }
    }

    // Fetch data and start cycling through rows
    fetchEnergyData();
    
    // Update displayed data every second
    setInterval(displayCurrentData, 1000);
  </script>
</body>
</html>
