const express = require('express');
const fs = require('fs');
const bodyParser = require('body-parser');
const app = express();

// Middleware to parse JSON requests
app.use(bodyParser.json());

// File where products will be stored
const productsFilePath = './products.json';

// Get products (from file)
app.get('/products', (req, res) => {
    fs.readFile(productsFilePath, 'utf8', (err, data) => {
        if (err) {
            return res.status(500).json({ message: 'Error reading products file' });
        }
        const products = JSON.parse(data || '[]');
        res.json(products);
    });
});

// Add a new product (to file)
app.post('/products', (req, res) => {
    const newProduct = req.body;

    fs.readFile(productsFilePath, 'utf8', (err, data) => {
        if (err) {
            return res.status(500).json({ message: 'Error reading products file' });
        }

        const products = JSON.parse(data || '[]');
        products.push(newProduct);

        fs.writeFile(productsFilePath, JSON.stringify(products, null, 2), (err) => {
            if (err) {
                return res.status(500).json({ message: 'Error saving products file' });
            }
            res.status(200).json({ message: 'Product added successfully' });
        });
    });
});

// Start server
const port = 3000;
app.listen(port, () => {
    console.log(`Server running at http://localhost:${port}`);
});
