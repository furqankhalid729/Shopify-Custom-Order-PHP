<h1>Laravel Shopify Order Processing System</h1>

<p>This project integrates Laravel with the Shopify PHP library to handle order creation directly from the Shopify Admin panel. It features a zip code validation system that ensures only orders with valid zip codes are processed.</p>

<h3>Key Features:</h3>
<ul>
<li>Zip Code Validation: When a user submits an order through the Shopify store, the system validates the zip code against a pre-defined list of valid zip codes.</li>
<li>Order Processing: If the zip code is valid, the order is successfully created and processed in Shopify.</li>
<li>Error Handling: If the zip code is invalid, the order is canceled, and the user is redirected to an error page with appropriate messaging.</li>
This application ensures smooth, automated order processing with robust zip code validation for better customer experience and fraud prevention. 
</ul>

<h3>Installation Instructions:</h3>
<ul>
    <li>
        <p>Install dependencies using Composer:</p>
        <pre><code>composer install</code></pre>
    </li>
    <li>
        <p>Create .env file and add these fields:</p>
        <pre><code>
ACCESS_TOKEN="Shopify Acces Token"
API_KEY="Shopify API KEY"
SECRET_KEY="Shopify Secret Key"
APP_HOST_NAME="Application URL"
SCOPE="Scope of Private Shopify App"
STORE_URL="Store URL"
        </code></pre>
    </li>
</ul>
