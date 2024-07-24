const express = require('express');
const router = express.Router();
const bcrypt = require('bcrypt');
const pool = require('../database');
const moment = require('moment-timezone'); // Import moment-timezone library
const fs = require('fs');
const path = require('path');
const multer = require('multer')
const nodemailer = require('nodemailer');
const axios = require('axios');
const { promisify } = require('util');
const pdf = require('html-pdf');
const puppeteer = require('puppeteer');
const jsPDF = require('jspdf');
const { PDFDocument, rgb, StandardFonts } = require('pdf-lib');
const { create } = require('pdf-creator-node');
// const baseurl = process.env.URL;
const baseurl = 'http://3.7.47.11';


// start email functionality
// Initialize nodemailer transporter
const transporter = nodemailer.createTransport({
    service: 'gmail',
    auth: {
        user: 'hello@earthbased.store', // Your Gmail address
        pass: 'EarthBasedsayshallo' // Your Gmail password
    },
    // transporter configuration
    debug: true
});

// Function to define email options
const mailOptions = (toEmail, mailsubject, template) => {
    let fromName = 'EarthBased';
    let fromEmail = 'hello@earthbased.store';
    return {
        from: `"${fromName}" <${fromEmail}>`, // Sender address
        to: toEmail, // List of recipients
        subject: mailsubject, // Subject line
        html: template // Use the HTML content from the template file
    };
};

// Function to define email options
const mailOptionswithAttachment = (toEmail, mailsubject, template, pdffile, pdfFilePath) => {
    let fromName = 'EarthBased';
    let fromEmail = 'hello@earthbased.store';
    return {
        // from: 'hello@earthbased.store', // Sender address
        from: `"${fromName}" <${fromEmail}>`, // Sender address
        to: toEmail, // List of recipients
        subject: mailsubject, // Subject line
        html: template, // Use the HTML content from the template file,
        attachments: [
            {
                filename: pdffile,
                path: pdfFilePath
            }
        ]
    };
};


// Function to define email options
const mailOptionswithAttachment2 = (toEmail, mailsubject, template, attachments) => {
    let fromName = 'EarthBased';
    let fromEmail = 'hello@earthbased.store';
    return {
        // from: 'hello@earthbased.store', // Sender address
        from: `"${fromName}" <${fromEmail}>`, // Sender address
        to: toEmail, // List of recipients
        subject: mailsubject, // Subject line
        html: template, // Use the HTML content from the template file,
        attachments: attachments // Array of attachment objects
    };
};


// Function to read HTML template file
const readHTMLTemplate = (filename) => {
    return new Promise((resolve, reject) => {
        const filePath = path.join(__dirname, filename);
        fs.readFile(filePath, 'utf8', (err, data) => {
            if (err) {
                reject(err);
            } else {
                resolve(data);
            }
        });
    });
};


// end email functionality


const checkCustomerExists = async (userID) => {
    return new Promise((resolve, reject) => {
        pool.query('SELECT * FROM customer WHERE id = ?', [userID], function (error, result) {
            if (error) {
                reject(error);
            } else {
                resolve(result.length > 0);
            }
        });
    });
};

const checkProductExists = async (productId) => {
    return new Promise((resolve, reject) => {
        pool.query('SELECT * FROM products WHERE ID = ?', [productId], function (error, result) {
            if (error) {
                reject(error);
            } else {
                resolve(result[0]);
            }
        });
    });
};

// Create a new orders
router.post('/', async (req, res) => {
    try {
        const { name, address, quantity, paymentType, productId, userId, finalAmount, status } = req.body;
        const missingFields = {};
        if (!address) missingFields.address = 'Address';
        if (!quantity) missingFields.quantity = 'Quantity';
        if (!name) missingFields.name = 'Name';
        if (!paymentType) missingFields.paymentType = 'Payment Type';
        if (!productId) missingFields.productId = 'Product Id';
        if (!userId) missingFields.userId = 'User Id';
        if (!finalAmount) missingFields.finalAmount = 'Final Amount';
        if (!status) missingFields.status = 'Status';

        if (Object.keys(missingFields).length > 0) {
            return res.status(400).json({ message: 'Missing required fields', missingFields });
        }

        const productExists = await checkProductExists(productId);
        if (!productExists) {
            return res.status(400).json({ message: 'Product does not exist' });
        }
        else {
            if (productExists.quantity < quantity) {
                return res.status(400).json({ message: productExists.quantity + ' is not available.' });
            }
        }

        const userExists = await checkCustomerExists(userId);
        if (!userExists) {
            return res.status(400).json({ message: 'User does not exist' });
        }

        await pool.query('UPDATE products SET quantity = quantity - ? WHERE id = ?', [quantity, productId], async function (productError) {
            if (productError) {
                console.error(error.message);
                return res.status(500).json({ message: 'Internal server error' });
            }
            await pool.query('INSERT INTO orders (name, address, quantity, paymentType, productID, userID, finalAmount, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                [name, address, quantity, paymentType, productId, userId, finalAmount, status], async function (error, result) {
                    if (error) {
                        console.error(error.message);
                        return res.status(500).json({ message: 'Internal server error' });
                    }
                    const insertedId = result.insertId;
                    await pool.query('SELECT * FROM orders WHERE id = ?', [insertedId], function (error, data) {
                        if (error) {
                            console.error(error.message);
                            return res.status(500).json({ message: 'Internal server error' });
                        }
                        if (data.length === 0) {
                            return res.status(404).json({ message: 'Customer not found' });
                        }
                        res.json(data[0]);
                    });
                });
        })
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});

// Get all orders items
router.get('/', async (req, res) => {
    try {
        await pool.query('SELECT orders.userID, orders.productID, customer.*, orders.*, products.* FROM orders JOIN customer ON orders.userID = customer.ID JOIN products ON orders.productID = products.ID;', function (error, result, fields) {
            if (error) {
                console.error(error.message);
                return res.status(500).json({ message: 'Internal server error' });
            }
            res.json(result);
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});

// Get a orders by ID
router.get('/:id', async (req, res) => {
    try {
        const { id } = req.params;
        await pool.query('SELECT * FROM orders WHERE id = ?', [id], function (error, data) {
            if (error) {
                console.error(error.message);
                return res.status(500).json({ message: 'Internal server error' });
            }
            if (data.length === 0) {
                return res.status(404).json({ message: 'Customer not found' });
            }
            res.json(data[0]);
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});

// Get the last order of a user by user ID
router.get('/last/:userId', async (req, res) => {
    try {
        const { userId } = req.params;
        await pool.query('SELECT * FROM orders WHERE userID = ? ORDER BY id DESC LIMIT 1', [userId], function (error, data) {
            if (error) {
                console.error(error.message);
                return res.status(500).json({ message: 'Internal server error' });
            }
            if (data.length === 0) {
                return res.json([{}]);
            }
            // Return the last order of the user
            res.json(data);
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});

// Get the last order of a user by user ID
router.get('/user/:userId', async (req, res) => {
    try {
        const { userId } = req.params;

        const userExists = await checkCustomerExists(userId);
        if (!userExists) {
            return res.status(400).json({ message: 'User does not exist' });
        }

        // await pool.query('SELECT orders.*, products.*, order.quantity as orderQuntity FROM orders JOIN products ON orders.productID = products.ID WHERE userID = ?', [userId], function (error, data) {
        //     if (error) {
        //         console.error(error.message);
        //         return res.status(500).json({ message: 'Internal server error' });
        //     }
        //     res.json(data);
        // });
        console.log(userId)
        await pool.query('SELECT *,day(ordered_date) as day, monthName(ordered_date) as month, year(ordered_date) as year from order_details WHERE customer_id = ? and status=1 ORDER BY ordered_date DESC', [userId], function (error, data) {
            if (error) {
                console.error(error.message);
                return res.status(500).json({ message: 'Internal server error' });
            }
            res.json(data);
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});


// Update a orders
router.put('/:id', async (req, res) => {
    try {
        const { id } = req.params;
        const { name, address, quantity, paymentType, productId, userId, finalAmount, status } = req.body;
        const missingFields = {};
        if (!address) missingFields.address = 'Address';
        if (!quantity) missingFields.quantity = 'Quantity';
        if (!name) missingFields.name = 'Name';
        if (!paymentType) missingFields.paymentType = 'Payment Type';
        if (!productId) missingFields.productId = 'Product Id';
        if (!userId) missingFields.userId = 'User Id';
        if (!finalAmount) missingFields.finalAmount = 'Final Amount';
        if (!status) missingFields.status = 'Status';

        if (Object.keys(missingFields).length > 0) {
            return res.status(400).json({ message: 'Missing required fields', missingFields });
        }

        const userExists = await checkCustomerExists(userId);
        if (!userExists) {
            return res.status(400).json({ message: 'User does not exist' });
        }

        await pool.query(
            // 'UPDATE orders SET name = ?, password = ?, email = ?, mobile = ?, user_type = ?, status = ? WHERE id = ?',
            'UPDATE orders SET name = ?,  quantity = ?, address = ?, paymentType = ?, productID = ?, finalAmount = ?, status = ?, userID = ? WHERE id = ?',
            // [name, hashedPassword, email, mobile, user_type, status, id],
            [name, quantity, address, paymentType, productId, finalAmount, status, userId, id],
            async function (error, result) {
                if (error) {
                    console.error(error.message);
                    return res.status(500).json({ message: 'Internal server error' });
                }
                res.json({ message: 'Customer updated successfully' });
            }
        );
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});

router.put('/status/:id', async (req, res) => {
    try {
        const { id } = req.params;
        const { status } = req.body;
        const missingFields = {};
        if (!status) missingFields.status = 'Status';

        if (Object.keys(missingFields).length > 0) {
            return res.status(400).json({ message: 'Missing required fields', missingFields });
        }

        await pool.query(
            // 'UPDATE orders SET name = ?, password = ?, email = ?, mobile = ?, user_type = ?, status = ? WHERE id = ?',
            'UPDATE orders SET status = ? WHERE id = ?',
            // [name, hashedPassword, email, mobile, user_type, status, id],
            [status, id], function (error, result) {
                if (error) {
                    console.error(error.message);
                    return res.status(500).json({ message: 'Internal server error' });
                }
                if (result.affectedRows === 0) {
                    return res.status(404).json({ message: 'Customer not found' });
                }
                res.json({ message: 'Customer deleted successfully' });
            });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});

// Delete a orders
router.delete('/:id', async (req, res) => {
    try {
        const { id } = req.params;

        await pool.query('DELETE FROM orders WHERE id = ?', [id], function (error, result) {
            if (error) {
                console.error(error.message);
                return res.status(500).json({ message: 'Internal server error' });
            }
            if (result.affectedRows === 0) {
                return res.status(404).json({ message: 'Customer not found' });
            }
            res.json({ message: 'Customer deleted successfully' });
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});



//new code

// const generateOrderId = () => {
//     const timestamp = Date.now().toString(); // Get current timestamp
//     const randomNum = Math.floor(Math.random() * 1000).toString().padStart(4, '0'); // Generate random 4-digit number
//     return timestamp + randomNum; // Combine timestamp and random number
// };

const generateOrderId = () => {
    //const timestamp = Date.now().toString(); // Get current timestamp
    const randomNum = Math.floor(Math.random() * 1000).toString().padStart(6, '0'); // Generate random 6-digit number
    return randomNum; // Combine timestamp and random number
};

// Create a new orders
router.post('/neworder', async (req, res) => {
    try {  

        const { payment_status, payment_type, shipping_charges, cod_charges, amount, coupon_details, payment_mode, customer_id, customer_name, customer_email, customer_contact, delivery_status,cart_type, customer_address, product_details, productswithqty, razorpay_details } = req.body;       
        // Get current date and time in Kolkata timezone
        const order_placed_date = moment().tz('Asia/Kolkata').format('YYYY-MM-DD HH:mm:ss');
        const cus_details = JSON.stringify(customer_address)
        const pr_details = JSON.stringify(product_details);
        const coupon_info = JSON.stringify(coupon_details);

        const parsed_coupon_info = JSON.parse(coupon_info);

        
        // Generate unique order ID
        const order_id = 'EBOR'+generateOrderId();
        
        // Loop through productswithqty array and insert order details for each product
        // console.log("Order Id",order_id)
        const invoiceNumbers = {}; // Object to store invoice numbers for each vendor ID
        for (const product of productswithqty) {
            
            // Generate unique Invoice Number
            // const invoice_number = 'EBIN'+generateOrderId();
            const vendorID = product.vendorID;
    
            // Check if an invoice number exists for the current vendor ID
            if (!invoiceNumbers.hasOwnProperty(vendorID)) {
                // If not, generate a new invoice number
                invoiceNumbers[vendorID] = 'EBIN' + generateOrderId();
            }
            // Use the generated invoice number for further processing
            const invoice_number = invoiceNumbers[vendorID];

            const totalSum = parsed_coupon_info[vendorID].totalSum;
            const couponsdata = parsed_coupon_info[vendorID].couponsdata;
            let coupon_code = couponsdata.code
            // Calculate discount based on coupon type
            let coupon_discount;
            if (couponsdata.type === 'Flat') {
                coupon_discount = couponsdata.amount;
            } else if (couponsdata.type === 'Percentage') {
                coupon_discount = totalSum * (couponsdata.amount / 100);
            }

            coupon_discount = coupon_discount.toFixed(2);
            

            const { attributes, productID, product_title, qty, product_type, product_price, product_img, variables_quantity } = product;
                        
            await pool.query('INSERT INTO order_details (order_id, invoice_number, payment_status, payment_type, shipping_charges, cod_charges, amount, coupon_code, coupon_discount, payment_mode, customer_id, customer_name, customer_email, customer_contact, delivery_status, customer_address, product_details, product_id, product_title, product_qty,product_type, product_price, product_img, razorpay_details, order_placed_date, ordered_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [order_id, invoice_number, payment_status, payment_type, shipping_charges, cod_charges, amount, coupon_code, coupon_discount, payment_mode, customer_id, customer_name, customer_email, customer_contact, delivery_status, cus_details, pr_details, productID, product_title, qty, product_type, product_price, product_img, razorpay_details, order_placed_date, order_placed_date], async function (productError) {
                if (productError) {
                    console.error(error.message);
                    return res.status(500).json({ message: 'Internal server error' });
                }
                if(product_type === 'simple'){
                    await pool.query('UPDATE productsnew SET quantity = quantity - ? WHERE id = ?', [qty, productID]);
                }else if(product_type === 'variable'){

                    const originalArray = JSON.parse(variables_quantity);
                    
                    const condition = JSON.parse(attributes); // Example condition
                    const decreaseBy = qty; // Example decrease by value                    

                    const newArray = originalArray?.map(obj => {
                        let newObj = { ...obj };
                        let shouldUpdate = true;
                    
                        for (const cond of condition) {
                            if (cond.name && obj[cond.name] !== cond.value) {
                                shouldUpdate = false;
                                break;
                            }
                        }
                    
                        if (shouldUpdate) {
                            newObj.quantity -= decreaseBy;
                        }
                    
                        return newObj;
                    });
                    
                    //console.log(newArray);
                    const variables_quantity_string = JSON.stringify(newArray)

                    await pool.query('UPDATE productsnew SET variables_quantity =  ? WHERE id = ?', [variables_quantity_string, productID]);
                }
                
                if(cart_type === 'cart'){
                    if(product_type === 'simple'){
                        await pool.query('DELETE FROM cart_products WHERE productID = ? and customerID = ?', [productID,customer_id]);
                    }else if(product_type === 'variable'){
                        await pool.query('DELETE FROM cart_products WHERE productID = ? and customerID = ? and attributes = ?', [productID,customer_id,attributes]);
                    }
                }
            })
        }
        //end products loop        
        
        res.json({ message: 'New Order created successfully' });
        
              
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});


// Create a new orders for buy product
router.post('/neworderbuyproduct', async (req, res) => {
    try {  

        const { payment_status, payment_type, shipping_charges, cod_charges, amount, coupon_details, payment_mode, customer_id, customer_name, customer_email, customer_contact, delivery_status,cart_type, customer_address, product_details, productswithqty, razorpay_details } = req.body;       
        // Get current date and time in Kolkata timezone
        const order_placed_date = moment().tz('Asia/Kolkata').format('YYYY-MM-DD HH:mm:ss');
        const cus_details = JSON.stringify(customer_address)
        const pr_details = JSON.stringify(product_details);

        const coupon_info = JSON.stringify(coupon_details);

        const parsed_coupon_info = JSON.parse(coupon_info);

        // Generate unique order ID
        const order_id = 'EBOR'+generateOrderId();
        
        // Loop through productswithqty array and insert order details for each product
        const invoiceNumbers = {}; // Object to store invoice numbers for each vendor ID
        for (const product of productswithqty) {
            
            // Generate unique Invoice Number
            // const invoice_number = 'EBIN'+generateOrderId();
            const vendorID = product.vendorID;
    
            // Check if an invoice number exists for the current vendor ID
            if (!invoiceNumbers.hasOwnProperty(vendorID)) {
                // If not, generate a new invoice number
                invoiceNumbers[vendorID] = 'EBIN' + generateOrderId();
            }
            // Use the generated invoice number for further processing
            const invoice_number = invoiceNumbers[vendorID];

            const totalSum = parsed_coupon_info[vendorID].totalSum;
            const couponsdata = parsed_coupon_info[vendorID].couponsdata;
            let coupon_code = couponsdata.code
            // Calculate discount based on coupon type
            let coupon_discount;
            if (couponsdata.type === 'Flat') {
                coupon_discount = couponsdata.amount;
            } else if (couponsdata.type === 'Percentage') {
                coupon_discount = totalSum * (couponsdata.amount / 100);
            }

            coupon_discount = coupon_discount.toFixed(2);
            

            const { attributes, productID, product_title, qty, product_type, product_price, product_img, variables_quantity } = product;
                        
            await pool.query('INSERT INTO order_details (order_id, invoice_number, payment_status, payment_type, shipping_charges, cod_charges, amount, coupon_code, coupon_discount, payment_mode, customer_id, customer_name, customer_email, customer_contact, delivery_status, customer_address, product_details, product_id, product_title, product_qty,product_type, product_price, product_img, razorpay_details, order_placed_date, ordered_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [order_id, invoice_number, payment_status, payment_type, shipping_charges, cod_charges, amount, coupon_code, coupon_discount,payment_mode, customer_id, customer_name, customer_email, customer_contact, delivery_status, cus_details, pr_details, productID, product_title, qty, product_type, product_price, product_img, razorpay_details, order_placed_date, order_placed_date], async function (productError) {
                if (productError) {
                    console.error(error.message);
                    return res.status(500).json({ message: 'Internal server error' });
                }
                if(product_type === 'simple'){
                    await pool.query('UPDATE productsnew SET quantity = quantity - ? WHERE id = ?', [qty, productID]);
                }else if(product_type === 'variable'){

                    // const data2 = await pool.query('SELECT variables_quantity from productsnew WHERE ID = ? and product_type = ?', [productID, product_type]);
                    // const originalArray = JSON.parse(data2[0].variables_quantity); // Parsing the JSON string from the database
    
                    const originalArray = JSON.parse(variables_quantity);
                    
                    const condition = JSON.parse(attributes); // Example condition
                    const decreaseBy = qty; // Example decrease by value                    

                    const newArray = originalArray?.map(obj => {
                        let newObj = { ...obj };
                        let shouldUpdate = true;
                    
                        for (const cond of condition) {
                            if (cond.name && obj[cond.name] !== cond.value) {
                                shouldUpdate = false;
                                break;
                            }
                        }
                    
                        if (shouldUpdate) {
                            newObj.quantity -= decreaseBy;
                        }
                    
                        return newObj;
                    });
                    
                    //console.log(newArray);
                    const variables_quantity_string = JSON.stringify(newArray)

                    await pool.query('UPDATE productsnew SET variables_quantity =  ? WHERE id = ?', [variables_quantity_string, productID]);
                }
                
                if(cart_type === 'cart'){
                    if(product_type === 'simple'){
                        await pool.query('DELETE FROM cart_products WHERE productID = ? and customerID = ?', [productID,customer_id]);
                    }else if(product_type === 'variable'){
                        await pool.query('DELETE FROM cart_products WHERE productID = ? and customerID = ? and attributes = ?', [productID,customer_id,attributes]);
                    }
                }else if(cart_type === 'buy_cart'){
                    if(product_type === 'simple'){
                        await pool.query('DELETE FROM buy_cart_products WHERE productID = ? and customerID = ?', [productID,customer_id]);
                    }else if(product_type === 'variable'){
                        await pool.query('DELETE FROM buy_cart_products WHERE productID = ? and customerID = ? and attributes = ?', [productID,customer_id,attributes]);
                    }
                }
                
                
                
                
            })
        }
        //end products loop
                
        
        res.json({ message: 'New Order created successfully'});
        
              
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});

// end buynow product order



// Create a new orders for guest
router.post('/neworderforguest', async (req, res) => {
    try {  
        // console.log(req.body);
        const { payment_status, payment_type, shipping_charges, cod_charges, amount, coupon_details, payment_mode, customer_type, customer_name, customer_email, customer_contact, delivery_status, customer_address, product_details, productswithqty, razorpay_details } = req.body;       
        // Get current date and time in Kolkata timezone
        const order_placed_date = moment().tz('Asia/Kolkata').format('YYYY-MM-DD HH:mm:ss');
        const cus_details = JSON.stringify(customer_address)
        const pr_details = JSON.stringify(product_details);
        const coupon_info = JSON.stringify(coupon_details);

        const parsed_coupon_info = JSON.parse(coupon_info);

        // // Accessing totalSum and couponsdata
        // // Assuming you want to access for vendor ID '37' as an example
        // const vendorID = '37';
        // const totalSum = parsed_coupon_info[vendorID].totalSum;
        // const couponsdata = parsed_coupon_info[vendorID].couponsdata;

        // Logging for demonstration
        // console.log('Total Sum:', totalSum);

        // Generate unique order ID
        const order_id = 'EBOR'+generateOrderId();
        
        // Loop through productswithqty array and insert order details for each product
        const invoiceNumbers = {}; // Object to store invoice numbers for each vendor ID
        for (const product of productswithqty) {
            
            // Generate unique Invoice Number
            // const invoice_number = 'EBIN'+generateOrderId();
            const vendorID = product.vendorID;
    
            // Check if an invoice number exists for the current vendor ID
            if (!invoiceNumbers.hasOwnProperty(vendorID)) {
                // If not, generate a new invoice number
                invoiceNumbers[vendorID] = 'EBIN' + generateOrderId();
            }
            // Use the generated invoice number for further processing
            const invoice_number = invoiceNumbers[vendorID];
            const totalSum = parsed_coupon_info[vendorID].totalSum;
            const couponsdata = parsed_coupon_info[vendorID].couponsdata;
            let coupon_code = couponsdata.code
            // Calculate discount based on coupon type
            let coupon_discount;
            if (couponsdata.type === 'Flat') {
                coupon_discount = couponsdata.amount;
            } else if (couponsdata.type === 'Percentage') {
                coupon_discount = totalSum * (couponsdata.amount / 100);
            }

            coupon_discount = coupon_discount.toFixed(2);
            
            const { attributes, productID, product_title, qty, product_type, product_price, product_img, variables_quantity } = product;
                        
            await pool.query('INSERT INTO order_details (order_id, invoice_number, payment_status, payment_type, shipping_charges, cod_charges, amount, coupon_code, coupon_discount, payment_mode,  customer_type, customer_name, customer_email, customer_contact, delivery_status, customer_address, product_details, product_id, product_title, product_qty,product_type, product_price, product_img, razorpay_details, order_placed_date, ordered_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [order_id, invoice_number, payment_status, payment_type, shipping_charges, cod_charges, amount, coupon_code, coupon_discount,payment_mode, customer_type, customer_name, customer_email, customer_contact, delivery_status, cus_details, pr_details, productID, product_title, qty, product_type, product_price, product_img, razorpay_details, order_placed_date, order_placed_date], async function (productError) {
                if (productError) {
                    console.error(productError.message);
                    return res.status(500).json({ message: 'Internal server error' });
                }
                if(product_type === 'simple'){
                    await pool.query('UPDATE productsnew SET quantity = quantity - ? WHERE id = ?', [qty, productID]);
                }else if(product_type === 'variable'){

                    // const data2 = await pool.query('SELECT variables_quantity from productsnew WHERE ID = ? and product_type = ?', [productID, product_type]);
                    // const originalArray = JSON.parse(data2[0].variables_quantity); // Parsing the JSON string from the database
    
                    const originalArray = JSON.parse(variables_quantity);
                    
                    const condition = JSON.parse(attributes); // Example condition
                    const decreaseBy = qty; // Example decrease by value                    

                    const newArray = originalArray?.map(obj => {
                        let newObj = { ...obj };
                        let shouldUpdate = true;
                    
                        for (const cond of condition) {
                            if (cond.name && obj[cond.name] !== cond.value) {
                                shouldUpdate = false;
                                break;
                            }
                        }
                    
                        if (shouldUpdate) {
                            newObj.quantity -= decreaseBy;
                        }
                    
                        return newObj;
                    });
                    
                    //console.log(newArray);
                    const variables_quantity_string = JSON.stringify(newArray)

                    await pool.query('UPDATE productsnew SET variables_quantity =  ? WHERE id = ?', [variables_quantity_string, productID]);
                }
            })
        }
        //end products loop        
        res.json({ message: 'New Order created successfully' });
        
              
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});






// // Mail sent for Confirmation Customer Order 
// router.post('/confirmmail', async (req, res) => {
//     try {
//         const { email, order_ids } = req.body;
//        console.log(req.body)

//         // Fetch HTML template from the PHP file
//         const response = await axios.post(baseurl+'/backendlive/adminnew/src/emailTemplates/order_confirm_customer.php', req.body);
        
//         // Extract HTML content from the response
//         const htmlTemplate = response.data;

//         const subject = 'Voila! Your Order is Confirmed - ' + order_ids[0];

//         // Send email using nodemailer transporter
//         transporter.sendMail(mailOptions(email, subject, htmlTemplate), function (error, info) {
//             if (error) {
//                 console.error(error);
//                 return res.status(500).json({ message: 'Failed to send email' });
//             } else {
//                 console.log('Email sent:', info.response);
//                 return res.status(200).json({ message: 'Email sent successfully' });
//             }
//         });
//     } catch (error) {
//         console.error(error);

//         if (error.response && error.response.status === 404) {
//             // Handle 404 error (PHP file not found)
//             return res.status(404).json({ message: 'HTML template file not found' });
//         } else {
//             // Handle other errors
//             return res.status(500).json({ message: 'Internal server error' });
//         }
//     }
// });


// Mail sent for New Order Alert to Admin/Vendor 
// router.post('/neworderalert', async (req, res) => {
//     try {
//         const { order_ids } = req.body;
//         //console.log(req.body)

//         // Fetch HTML template from the PHP file
//         const response = await axios.post(baseurl+'/backendlive/adminnew/src/emailTemplates/Admin_NewOrder.php', req.body);
        
//         // Extract HTML content from the response
//         const htmlTemplate = response.data;

//         const subject = 'New Order Placed - ' + order_ids[0];
//         const admin_email  = 'support@earthbased.store'; 
//         // Send email using nodemailer transporter
//         transporter.sendMail(mailOptions(admin_email, subject, htmlTemplate), function (error, info) {
//             if (error) {
//                 console.error(error);
//                 return res.status(500).json({ message: 'Failed to send email' });
//             } else {
//                 console.log('Email sent:', info.response);
//                 return res.status(200).json({ message: 'Email sent successfully' });
//             }
//         });
//     } catch (error) {
//         console.error(error);

//         if (error.response && error.response.status === 404) {
//             // Handle 404 error (PHP file not found)
//             return res.status(404).json({ message: 'HTML template file not found' });
//         } else {
//             // Handle other errors
//             return res.status(500).json({ message: 'Internal server error' });
//         }
//     }
// });


// Mail sent for New Order Processing Notification 
router.post('/orderemails', async (req, res) => {
    try {
        // console.log(req.body)
        const { userType, userEmail, adminEmail, vendorEmail, orderId, invoice_number, status } = req.body;

        if(status === 'Confirmed'){
            //console.log("confirmed")

            let updatedstatus = 1;
            let delivery_status = 'confirmed';
            const confirmed_date = moment().tz('Asia/Kolkata').format('YYYY-MM-DD HH:mm:ss');
                await pool.query(
                    'UPDATE order_details SET confirmed_status = ?,confirmed_date = ?, delivery_status= ? WHERE order_id = ? and invoice_number = ?',
                    // [name, hashedPassword, email, mobile, user_type, status, id],
                    [updatedstatus, confirmed_date, delivery_status, orderId, invoice_number], function (error, result) {
                        if (error) {
                            console.error(error.message);
                            return res.status(500).json({ message: 'Internal server error' });
                        }
                        const subject = 'Your Order Is Being Processed - ' + orderId;
                        req.body.title = subject;
                        axios.post(baseurl+'/backendlive/adminnew/src/emailTemplates/order_processing.php', req.body)
                        .then(response => {
                            const htmlTemplate = response.data;
                            //console.log(htmlTemplate); // Check if the response is not empty
                           

                            const email1 = userEmail;
                            // const email1 = 'simhapune@gmail.com';
                            // // Send email using nodemailer transporter
                            transporter.sendMail(mailOptions(email1, subject, htmlTemplate), function (error, info) {
                                if (error) {
                                    console.error(error);
                                    return res.status(500).json({ message: 'Failed to send email' });
                                } else {
                                    console.log('Email sent:', info.response);
                                    return res.status(200).json({ message: 'Customer Order is Confirmed.' });
                                }
                            });
                        })
                        .catch(error => {
                            console.error('Error fetching data:', error);
                        });                    
                });
            
        }else if(status === 'Shipped'){
            const updatedstatus = 1;
            const delivery_status = 'shipped'; //delivered
            const shipped_date = moment().tz('Asia/Kolkata').format('YYYY-MM-DD HH:mm:ss');
            await pool.query(
                'UPDATE order_details SET shipped_status = ?,shipped_date = ?, delivery_status= ? WHERE order_id = ? and invoice_number = ?',
                // [name, hashedPassword, email, mobile, user_type, status, id],
                [updatedstatus, shipped_date, delivery_status, orderId, invoice_number], function (error, result) {
                    if (error) {
                        console.error(error.message);
                        return res.status(500).json({ message: 'Internal server error' });
                    }
                    // let subject = 'Your Order Is on Its Way - ' + orderId;
                    let subject = 'Order Shipped - ' + orderId;
                    req.body.title = subject;
                    axios.post(baseurl+'/backendlive/adminnew/src/emailTemplates/order_status.php', req.body)
                            .then(response => {
                                const htmlTemplate = response.data;
                                //console.log(htmlTemplate); // Check if the response is not empty
                                
                                const email1 = userEmail;
                                
                                // Send email using nodemailer transporter
                                transporter.sendMail(mailOptions(email1, subject, htmlTemplate), function (error, info) {
                                    if (error) {
                                        console.error(error);
                                        return res.status(500).json({ message: 'Failed to send email' });
                                    } else {
                                        console.log('Email sent:', info.response);
                                    }
                                });
                            })
                            .then(() => {
                                // Second axios.post call
                                subject = 'Order Shipped - ' + orderId;
                                req.body.title = subject;
                                req.body.usertype = 'Admin';
                                //Admin Email
                                return axios.post(baseurl+'/backendlive/adminnew/src/emailTemplates/order_sharing.php', req.body)
                                        .then((response1) => {

                                            const htmlTemplate = response1.data;
                                            
                                            // Send email using nodemailer transporter
                                            transporter.sendMail(mailOptions(adminEmail, subject, htmlTemplate), function (error, info) {
                                                if (error) {
                                                    console.error(error);
                                                    return res.status(200).json({ message: 'Failed to send email' });
                                                } else {
                                                    console.log('Email sent:', info.response);
                                                    return res.status(200).json({ message: 'Customer Order is Shipped.' });
                                                }
                                            });
                                            
                                            // Third axios.post call
                                            // Vendor email
                                            subject = 'Order Shipped - ' + orderId;
                                            req.body.title = subject;
                                            req.body.usertype = 'Vendor';
                                            return axios.post(baseurl+'/backendlive/adminnew/src/emailTemplates/order_sharing.php', req.body)
                                                .then((response2) => {

                                                    const htmlTemplate = response2.data;

                                                    // Send email using nodemailer transporter
                                                    transporter.sendMail(mailOptions(vendorEmail, subject, htmlTemplate), function (error, info) {
                                                        if (error) {
                                                            console.error(error);
                                                            return res.status(500).json({ message: 'Failed to send email' });
                                                        } else {
                                                            console.log('Email sent:', info.response);
                                                            return res.status(200).json({ message: 'Customer Order is Shipped.' });
                                                        }
                                                    });
                                                });
                                        });
                                
                                
                            })
                            .then(response => {
                                // Handle the response of the third request if needed
                                return res.status(200).json({ message: 'Customer Order is Shipped.' });
                            })
                            .catch(error => {
                                console.error('Error fetching data:', error);
                                return res.status(500).json({ message: 'Failed to process order.' });
                            });

            });
        }else if(status === 'Delivered'){
            let updatedstatus = 1;
            let delivery_status = 'delivered'; //
            const delivered_date = moment().tz('Asia/Kolkata').format('YYYY-MM-DD HH:mm:ss');
            await pool.query(
                'UPDATE order_details SET delivered_status = ?,delivered_date = ?, delivery_status= ? WHERE order_id = ? and invoice_number = ?',
                // [name, hashedPassword, email, mobile, user_type, status, id],
                [updatedstatus, delivered_date, delivery_status, orderId, invoice_number], function (error, result) {
                    if (error) {
                        console.error(error.message);
                        return res.status(500).json({ message: 'Internal server error' });
                    }
                    let subject = 'Your Order Has Been Delivered - ' + orderId;
                    req.body.title = subject;
                    axios.post(baseurl+'/backendlive/adminnew/src/emailTemplates/order_status.php', req.body)
                    // axios.post(baseurl+'/backendlive/adminnew/src/emailTemplates/order_delivered.php', req.body)
                            .then(response => {
                                const htmlTemplate = response.data;
                                //console.log(htmlTemplate); // Check if the response is not empty
                                
                                // const subject = 'Your Order Has Been Delivered - ' + orderId;
                                // const email1 = userEmail;
                                const email1 = userEmail;
                                
                                // Send email using nodemailer transporter
                                transporter.sendMail(mailOptions(email1, subject, htmlTemplate), function (error, info) {
                                    if (error) {
                                        console.error(error);
                                        return res.status(500).json({ message: 'Failed to send email' });
                                    } else {
                                        console.log('Email sent:', info.response);
                                    }
                                });
                            })
                            .then(() => {
                                // Second axios.post call
                                subject = 'Order Completed - ' + orderId;
                                req.body.title = subject;
                                req.body.usertype = 'Admin';
                                //Admin Email
                                return axios.post(baseurl+'/backendlive/adminnew/src/emailTemplates/order_sharing.php', req.body)
                                // return axios.post(baseurl+'/backendlive/adminnew/src/emailTemplates/Admin-OrderCompleted.php', req.body)
                                    .then((response1) => {

                                        const htmlTemplate = response1.data;

                                        // const subject = 'Order Completed - ' + orderId;
                                        // const email1 = userEmail;
                                        // Send email using nodemailer transporter
                                        transporter.sendMail(mailOptions(adminEmail, subject, htmlTemplate), function (error, info) {
                                            if (error) {
                                                console.error(error);
                                                return res.status(500).json({ message: 'Failed to send email' });
                                            } else {
                                                console.log('Email sent:', info.response);
                                                return res.status(200).json({ message: 'Customer Order is Completed.' });
                                            }
                                        });


                                        // Third axios.post call
                                        subject = 'Order Completed - ' + orderId;
                                        req.body.title = subject;
                                        req.body.usertype = 'Vendor';
                                        //Vendor Email
                                        return axios.post(baseurl+'/backendlive/adminnew/src/emailTemplates/order_sharing.php', req.body)

                                        // return axios.post(baseurl+'/backendlive/adminnew/src/emailTemplates/Vendor-OrderCompleted.php', req.body)
                                        .then((response1) => {

                                            const htmlTemplate = response1.data;

                                            // const subject = 'Order Completed - ' + orderId;
                                            // Send email using nodemailer transporter
                                            transporter.sendMail(mailOptions(vendorEmail, subject, htmlTemplate), function (error, info) {
                                                if (error) {
                                                    console.error(error);
                                                    return res.status(500).json({ message: 'Failed to send email' });
                                                } else {
                                                    console.log('Email sent:', info.response);
                                                    return res.status(200).json({ message: 'Customer Order is Completed.' });
                                                }
                                            });
                                        });


                                    });
                                
                            })
                            .then(response => {
                                // Handle the response of the third request if needed
                                return res.status(200).json({ message: 'Customer Order is Completed.' });
                            })
                            .catch(error => {
                                console.error('Error fetching data:', error);
                                return res.status(500).json({ message: 'Failed to process order.' });
                            });

            });
        }
        
        
    } catch (error) {
        console.error(error);

        if (error.response && error.response.status === 404) {
            // Handle 404 error (PHP file not found)
            return res.status(404).json({ message: 'HTML template file not found' });
        } else {
            // Handle other errors
            return res.status(500).json({ message: 'Internal server error' });
        }
    }
});

// Cronjob for sent to order mails to custmer and admin
// const cronjobsdata = async () => {
//     console.log("cronjob running for emails");
    
//     try {
//         const delivery_status = 'pending';
//         await pool.query('SELECT distinct invoice_number,customer_address,customer_name FROM order_details WHERE delivery_status = ?', [delivery_status], function (error, result) {
//             if (error) {
//                 console.error(error.message);
//                 //return res.status(500).json({ message: 'Internal server error' });
//             }
//             if (result.length === 0) {
//                 console.log('No pending orders found.');
//                 return;
//             }

//             for (const row of result) {
//                 // const { customer_email, order_id, customer_address } = row;

//                 // console.log(row);

//                 // Uncomment when email sending functionality is ready
//                 sendOrderConfirmationEmail(row);
//                 // Update delivery_status
//                 // pool.query('UPDATE order_details SET delivery_status = ? WHERE order_id = ?', ['ordered', row.order_id]);
//             }
//         });
//         // console.log('Mail sent successfully.');
//     } catch (error) {
//         console.error('Error:', error.message);

//         if (error.response && error.response.status === 404) {
//             console.log("HTML template file not found");
//         } else {
//             console.log('Internal server error');
//         }
//     }
// }

// const sendOrderConfirmationEmail = async (record) => {
//     try {
//         // Make POST request to fetch email template
//         const response = await axios.post(baseurl+'/backendlive/adminnew/src/emailTemplates/order_confirm_customer_all.php', record);
//         const htmlTemplate = response.data;

//         // console.log(htmlTemplate);
//         const subject = 'Voila! Your Order is Confirmed - ' + record.order_id;

//         // console.log(record[0].customer_email)
        
//         let email = record.customer_email;
//         // Send email
//         transporter.sendMail(mailOptions(email, subject, htmlTemplate), function (error, info) {
//             if (error) {
//                 console.error(error);
//                 return res.status(500).json({ message: 'Failed to send email' });
//             } else {
//                 console.log('Email sent:', info.response);
//                 return res.status(200).json({ message: 'Email sent successfully' });
//             }
//         });
        
//         //console.log('Email sent to', email);


//         // Fetch HTML template from the PHP file
//         const response1 = await axios.post(baseurl+'/backendlive/adminnew/src/emailTemplates/Admin_NewOrder.php', record);
        
//         // Extract HTML content from the response
//         const htmlTemplate1 = response1.data;

//         const subject1 = 'New Order Placed - ' + record.order_id;
//         // const admin_email  = 'support@earthbased.store';
//         // Send email using nodemailer transporter
//         transporter.sendMail(mailOptions(admin_email, subject1, htmlTemplate1), function (error, info) {
//             if (error) {
//                 console.error(error);
//                 return res.status(500).json({ message: 'Failed to send email' });
//             } else {
//                 console.log('Email sent:', info.response);
//                 return res.status(200).json({ message: 'Email sent successfully' });
//             }
//         });
        
      
//     } catch (error) {
//         console.error('Failed to send email:', error.message);
//     }
// }





const queryAsync = promisify(pool.query).bind(pool);

const sendEmail = async (email, subject, htmlTemplate) => {
    try {
        await transporter.sendMail(mailOptions(email, subject, htmlTemplate));
        console.log('Email sent to', email);
    } catch (error) {
        console.error('Failed to send email:', error);
        throw new Error('Failed to send email');
    }
};

const cronjobsdata = async () => {
    console.log("Cronjob running for emails");

    try {
        const delivery_status = 'pending';
        
        const result = await queryAsync('SELECT distinct order_id,date(ordered_date) as ordered_date,payment_type,customer_address,customer_name,customer_email FROM order_details WHERE delivery_status = ?', [delivery_status]);

        if (result.length === 0) {
            console.log('No pending orders found.');
            return;
        }

        for (const row of result) {
            // await sendOrderConfirmationEmail(row);
            await sendOrderConfirmationEmailwithAttachments(row.order_id,row.customer_email);

            // Update delivery_status
            await updateDeliveryStatus(row.order_id);
        }

        console.log('Emails sent successfully.');
    } catch (error) {
        console.error('Error:', error.message);

        if (error.response && error.response.status === 404) {
            console.log("HTML template file not found");
        } else {
            console.log('Internal server error');
        }
    }
};

const updateDeliveryStatus = async (order_id, custmer_email) => {
    try {
        const result = await queryAsync('UPDATE order_details SET delivery_status = ?, processed = ? WHERE order_id = ?', ['ordered', true, order_id]);
        console.log(`Delivery status updated for Order ID: ${order_id}`);
        console.log(result); // Log the result of the update query
    } catch (error) {
        console.error('Failed to update delivery status:', error.message);
        throw new Error('Failed to update delivery status');
    }
};

const sendOrderConfirmationEmailwithAttachments = async (order_id, customer_email) => {
    console.log("Cronjob running for vendor emails");

    try {
        // const order_id = order_id
        const result = await queryAsync('SELECT GROUP_CONCAT(a.product_id) as productids,GROUP_CONCAT(DISTINCT a.invoice_number) as invoice_number, GROUP_CONCAT(DISTINCT a.coupon_discount) as coupon_discount, GROUP_CONCAT(DISTINCT a.customer_address) as customer_address, (SELECT d.email FROM admin_users d WHERE id IN (SELECT b.user_id FROM vendors b WHERE b.vendorID IN(SELECT c.vendor_id FROM productsnew c WHERE c.ID=a.product_id ))) as vendor_email FROM order_details a WHERE order_id=? GROUP by vendor_email', [order_id]);

        if (result.length === 0) {
            console.log('No pending orders found.');
            return;
        }

        // Initialize an array to store attachment objects
        const pdfAttachments = [];
        let discount_total = 0;
        // vendor Emails
        for (const row of result) {
            row['order_id'] = order_id;
            discount_total = discount_total + parseFloat(row['coupon_discount']);
            // row['order_id'] = order_id;
            // console.log(row['invoice_number']);
            const file_name = order_id + '_' + row['invoice_number'] + '.pdf';
            const file_path = baseurl+'/backendlive/adminnew/src/emailTemplates/invoice/' + file_name;

            // Create an attachment object and push it to the pdfAttachments array
            pdfAttachments.push({
                filename: file_name,
                path: file_path
            });

            // Generate the invoice template
            const response = await axios.post(baseurl+'/backendlive/adminnew/src/emailTemplates/order_invoice.php', row);
            const htmlTemplate = response.data;
            // console.log(htmlTemplate)
       
            const mpdfData = { html: htmlTemplate, order_id: order_id, invoice_number: row['invoice_number']};

            const response1 = await axios.post(baseurl+'/backendlive/adminnew/src/emailTemplates/generate_invoice.php', mpdfData);
            //const htmlTemplate1 = response1.data;

            
            // await sendEmailAttachment(customerEmail, customerSubject, customerHtmlTemplate, file_name, file_path);

            // Email Templates for vendor
            const vendorTemplateResponse = await axios.post(baseurl+'/backendlive/adminnew/src/emailTemplates/Vendor_NewOrderPlaced.php', row);
                const vendorHtmlTemplate = vendorTemplateResponse.data;
                // console.log(vendorHtmlTemplate);
                const vendorSubject = 'New Order Placed - ' + order_id;
                const vendorEmail = row['vendor_email']; // Change this to your vendor email address
                //const vendorEmail = 'rksimhadri731@gmail.com'; // Change this to your vendor email address

            
            // await sendEmailAttachment(vendorEmail, vendorSubject, vendorHtmlTemplate, file_name, file_path);
            await sendEmail(vendorEmail, vendorSubject, vendorHtmlTemplate);

        }
        // End loop each order

        //record['order_id'] = order_id;
        let record = {
            order_id,
            discount_total
        }
        // Email Templates for Customer
        const customerTemplateResponse = await axios.post(baseurl+'/backendlive/adminnew/src/emailTemplates/order_confirm_customer_all.php', record);
        const customerHtmlTemplate = customerTemplateResponse.data;
        const customerSubject = 'Voila! Your Order is Confirmed - ' + order_id;
        const customerEmail = customer_email; // Change this to your customer email address
        // const customerEmail = 'sreenath@earthbased.store,revanthkings@gmail.com'; // Change this to your customer email address

        await sendEmailAllAttachments(customerEmail, customerSubject, customerHtmlTemplate, pdfAttachments);

        // Email Templates for Admin
        const adminTemplateResponse = await axios.post(baseurl+'/backendlive/adminnew/src/emailTemplates/Admin_NewOrder.php', record);
        const adminHtmlTemplate = adminTemplateResponse.data;
        const adminSubject = 'New Order Placed - ' + order_id;
        
       
        const adminEmail  = 'support@earthbased.store'; // Change this to your admin email address

        // await sendEmailAllAttachments(adminEmail, adminSubject, adminHtmlTemplate, pdfAttachments);
        await sendEmail(adminEmail, adminSubject, adminHtmlTemplate);

        // console.log('Emails sent successfully.');
    } catch (error) {
        console.error('Error:', error.message);

        if (error.response && error.response.status === 404) {
            console.log("HTML template file not found");
        } else {
            console.log('Internal server error');
        }
    }
};

// Call cronjobsdata every 15 seconds
setInterval(cronjobsdata, 15000);

const sendEmailAttachment = async (email, subject, htmlTemplate, file_name, filepath) => {
    try {
        await transporter.sendMail(mailOptionswithAttachment(email, subject, htmlTemplate, file_name, filepath));
        console.log('Email sent to', email);
    } catch (error) {
        console.error('Failed to send email:', error);
        throw new Error('Failed to send email');
    }
};

const sendEmailAllAttachments = async (email, subject, htmlTemplate, attachments) => {
    try {
        await transporter.sendMail(mailOptionswithAttachment2(email, subject, htmlTemplate, attachments));
        console.log('Email sent to', email);
        // Remove the PDF file
        // fs.unlink(filepath, (err) => {
        //     if (err) {
        //         console.error('Failed to delete PDF file:', err);
        //         // Handle error, if required
        //     } else {
        //         console.log('PDF file deleted successfully');
        //     }
        // });

    } catch (error) {
        console.error('Failed to send email:', error);
        throw new Error('Failed to send email');
    }
};

const sendOrderConfirmationEmailwithAttachment = async (record) => {
    try {
        // Generate the invoice template
        const response = await axios.post(baseurl+'/backendlive/adminnew/src/emailTemplates/order_invoice.php', record);
        const htmlTemplate = response.data;
       
        const mpdfData = { html: htmlTemplate, invoice_number: record.invoice_number};

        const response1 = await axios.post(baseurl+'/backendlive/adminnew/src/emailTemplates/generate_invoice.php', mpdfData);
        // const htmlTemplate1 = response1.data;



        // Email Templates
        const response2 = await axios.post(baseurl+'/backendlive/adminnew/src/emailTemplates/order_confirm_customer_all.php', record);
        const htmlTemplate2 = response2.data;
        const subject2 = 'Voila! Your Order is Confirmed - ' + record.invoice_number;

        // For Customer email
        let file_name = record.invoice_number+'.pdf';
        let file_path = baseurl+'/backendlive/adminnew/src/emailTemplates/invoice/'+record.invoice_number+'.pdf';
        // await sendEmailAttachment(record.customer_email, subject, htmlTemplate2, file_name, file_path);
        await sendEmailAttachment(record.customer_email, subject2, htmlTemplate2, file_name, file_path);

        // For Admin email
        const adminTemplateResponse = await axios.post(baseurl+'/backendlive/adminnew/src/emailTemplates/Admin_NewOrder.php', record);
        const adminHtmlTemplate = adminTemplateResponse.data;
        const adminSubject = 'New Order Placed - ' + record.invoice_number;
        const adminEmail = 'hello@earthbased.store'; // Change this to your admin email address

        await sendEmailAttachment(adminEmail, adminSubject, adminHtmlTemplate, file_name, file_path);


        // For Vendor email
        const invoice_number = record.invoice_number;
        const result = await queryAsync('SELECT GROUP_CONCAT(a.product_id) as productids,(SELECT d.email FROM admin_users d WHERE id IN (SELECT b.user_id FROM vendors b WHERE b.vendorID IN(SELECT c.vendor_id FROM productsnew c WHERE c.ID=a.product_id ))) as vendor_email FROM order_details a WHERE invoice_number=? GROUP by vendor_email', [invoice_number]);

        if (result.length === 0) {
            console.log('No pending orders found.');
            return;
        }

        for (const row of result) {
            row['invoice_number'] = invoice_number;
            // console.log(row['vendor_email'])
            

            // Email Templates
            const vendorTemplateResponse = await axios.post(baseurl+'/backendlive/adminnew/src/emailTemplates/Vendor_NewOrderPlaced.php', row);
            const vendorHtmlTemplate = vendorTemplateResponse.data;
            // console.log(vendorHtmlTemplate);
            const vendorSubject = 'New Order Placed - ' + invoice_number;
            const vendorEmail = row['vendor_email']; // Change this to your vendor email address
    
            await sendEmail(vendorEmail, vendorSubject, vendorHtmlTemplate);
        }
    } catch (error) {
        console.error('Failed to send email:', error.message);
    }
};




const cronjobsdatatesting = async () => {
    console.log("Cronjob running for vendor emails");

    try {
        const order_id = 'EBOR000976';
        const result = await queryAsync('SELECT GROUP_CONCAT(a.product_id) as productids,GROUP_CONCAT(DISTINCT a.invoice_number) as invoice_number, GROUP_CONCAT(DISTINCT a.coupon_discount) as coupon_discount, GROUP_CONCAT(DISTINCT a.customer_address) as customer_address, (SELECT d.email FROM admin_users d WHERE id IN (SELECT b.user_id FROM vendors b WHERE b.vendorID IN(SELECT c.vendor_id FROM productsnew c WHERE c.ID=a.product_id ))) as vendor_email FROM order_details a WHERE order_id=? GROUP by vendor_email', [order_id]);
        console.log(result.length)
        if (result.length === 0) {
            console.log('No pending orders found.');
            return;
        }

        // Initialize an array to store attachment objects
        const pdfAttachments = [];
        let discount_total = 0;
        // vendor Emails
        for (const row of result) {
            row['order_id'] = order_id;
            discount_total = discount_total + parseFloat(row['coupon_discount']);
            // row['order_id'] = order_id;
            // console.log(row);
            const file_name = order_id + '_' + row['invoice_number'] + '.pdf';
            const file_path = baseurl+'/backendlive/adminnew/src/emailTemplates/invoice/' + file_name;

            // Create an attachment object and push it to the pdfAttachments array
            pdfAttachments.push({
                filename: file_name,
                path: file_path
            });

            // Generate the invoice template
            const response = await axios.post(baseurl+'/backendlive/adminnew/src/emailTemplates/order_invoice.php', row);
            const htmlTemplate = response.data;
            // console.log(htmlTemplate)
       
            const mpdfData = { html: htmlTemplate, order_id: order_id, invoice_number: row['invoice_number']};

            const response1 = await axios.post(baseurl+'/backendlive/adminnew/src/emailTemplates/generate_invoice.php', mpdfData);
            //const htmlTemplate1 = response1.data;

            
            // await sendEmailAttachment(customerEmail, customerSubject, customerHtmlTemplate, file_name, file_path);

            // Email Templates for vendor
            const vendorTemplateResponse = await axios.post(baseurl+'/backendlive/adminnew/src/emailTemplates/Vendor_NewOrderPlaced.php', row);
            const vendorHtmlTemplate = vendorTemplateResponse.data;
            // console.log(vendorHtmlTemplate);
            const vendorSubject = 'New Order Placed - ' + order_id;
            //const vendorEmail = row['vendor_email']; // Change this to your vendor email address
            const vendorEmail = 'rksimhadri731@gmail.com'; // Change this to your vendor email address

            
            await sendEmailAttachment(vendorEmail, vendorSubject, vendorHtmlTemplate, file_name, file_path);





        }
        // End loop each order

        //record['order_id'] = order_id;
        let record = {
            order_id,
            discount_total
        }
        // Email Templates for Customer
        const customerTemplateResponse = await axios.post(baseurl+'/backendlive/adminnew/src/emailTemplates/order_confirm_customer_all.php', record);
        const customerHtmlTemplate = customerTemplateResponse.data;
        const customerSubject = 'Voila! Your Order is Confirmed - ' + order_id;
        //const customerEmail = row['vendor_email']; // Change this to your customer email address
        const customerEmail = 'simhapune@gmail.com'; // Change this to your customer email address
        // const customerEmail = 'sreenath@earthbased.store,revanthkings@gmail.com'; // Change this to your customer email address

        //await sendEmailAllAttachments(customerEmail, customerSubject, customerHtmlTemplate, pdfAttachments);

        // Email Templates for Admin
        const adminTemplateResponse = await axios.post(baseurl+'/backendlive/adminnew/src/emailTemplates/Admin_NewOrder.php', record);
        const adminHtmlTemplate = adminTemplateResponse.data;
        const adminSubject = 'New Order Placed - ' + order_id;
        //const adminEmail = row['vendor_email']; // Change this to your admin email address
        const adminEmail = 'rksimhadri731@gmail.com'; // Change this to your admin email address
        // const adminEmail  = 'support@earthbased.store';
        // const adminEmail = 'sreenath@earthbased.store,revanthkings@gmail.com'; // Change this to your admin email address

        //await sendEmailAllAttachments(adminEmail, adminSubject, adminHtmlTemplate, pdfAttachments);

        // console.log('Emails sent successfully.');
    } catch (error) {
        console.error('Error:', error.message);

        if (error.response && error.response.status === 404) {
            console.log("HTML template file not found");
        } else {
            console.log('Internal server error');
        }
    }
};

// cronjobsdatatesting();
// end cronjob mails functionality

module.exports = router;
