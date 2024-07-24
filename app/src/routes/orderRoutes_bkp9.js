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
    return {
        from: 'hello@earthbased.store', // Sender address
        to: toEmail, // List of recipients
        subject: mailsubject, // Subject line
        html: template // Use the HTML content from the template file
    };
};

// Function to define email options
const mailOptionswithAttachment = (toEmail, mailsubject, template, pdffile, pdfFilePath) => {
    return {
        from: 'hello@earthbased.store', // Sender address
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

const generateOrderId = () => {
    const timestamp = Date.now().toString(); // Get current timestamp
    const randomNum = Math.floor(Math.random() * 1000).toString().padStart(4, '0'); // Generate random 4-digit number
    return timestamp + randomNum; // Combine timestamp and random number
};

// Create a new orders
router.post('/neworder', async (req, res) => {
    try {  

        const { payment_status, payment_type, shipping_charges, cod_charges, amount, payment_mode, customer_id, customer_name, customer_email, customer_contact, delivery_status,cart_type, customer_address, product_details, productswithqty, razorpay_details } = req.body;       
        // Get current date and time in Kolkata timezone
        const order_placed_date = moment().tz('Asia/Kolkata').format('YYYY-MM-DD HH:mm:ss');
        const cus_details = JSON.stringify(customer_address)
        const pr_details = JSON.stringify(product_details);

        // Loop through productswithqty array and insert order details for each product
        let order_ids = [];
        let email_data;
        const invoice_number = 'Invoice_EB_'+generateOrderId();
        for (const product of productswithqty) {
            // Generate unique order ID

            const order_id = 'order_'+generateOrderId();
            order_ids.push(order_id);
            
            email_data = {
                order_ids:order_ids,
                email:customer_address.email,
                customer_name
            }

            const { attributes, productID, product_title, qty, product_type, product_price, product_img, variables_quantity } = product;
                        
            await pool.query('INSERT INTO order_details (order_id, invoice_number, payment_status, payment_type, shipping_charges, cod_charges, amount, payment_mode, customer_id, customer_name, customer_email, customer_contact, delivery_status, customer_address, product_details, product_id, product_title, product_qty,product_type, product_price, product_img, razorpay_details, order_placed_date, ordered_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [order_id, invoice_number, payment_status, payment_type, shipping_charges, cod_charges, amount, payment_mode, customer_id, customer_name, customer_email, customer_contact, delivery_status, cus_details, pr_details, productID, product_title, qty, product_type, product_price, product_img, razorpay_details, order_placed_date, order_placed_date], async function (productError) {
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
                }
            })
        }
        //end products loop        
        
        res.json({ message: 'New Order created successfully', email_data: email_data });
        
              
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});


// Create a new orders for buy product
router.post('/neworderbuyproduct', async (req, res) => {
    try {  

        const { payment_status, payment_type, shipping_charges, cod_charges, amount, payment_mode, customer_id, customer_name, customer_email, customer_contact, delivery_status,cart_type, customer_address, product_details, productswithqty, razorpay_details } = req.body;       
        // Get current date and time in Kolkata timezone
        const order_placed_date = moment().tz('Asia/Kolkata').format('YYYY-MM-DD HH:mm:ss');
        const cus_details = JSON.stringify(customer_address)
        const pr_details = JSON.stringify(product_details);

        // Loop through productswithqty array and insert order details for each product
        let order_ids = [];
        let email_data;
        const invoice_number = 'Invoice_EB_'+generateOrderId();
        for (const product of productswithqty) {
            // Generate unique order ID

            const order_id = 'order_'+generateOrderId();
            order_ids.push(order_id);
            
            email_data = {
                order_ids:order_ids,
                email:customer_address.email,
                customer_name
            }

            const { attributes, productID, product_title, qty, product_type, product_price, product_img, variables_quantity } = product;
                        
            await pool.query('INSERT INTO order_details (order_id, invoice_number, payment_status, payment_type, shipping_charges, cod_charges, amount, payment_mode, customer_id, customer_name, customer_email, customer_contact, delivery_status, customer_address, product_details, product_id, product_title, product_qty,product_type, product_price, product_img, razorpay_details, order_placed_date, ordered_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [order_id, invoice_number, payment_status, payment_type, shipping_charges, cod_charges, amount, payment_mode, customer_id, customer_name, customer_email, customer_contact, delivery_status, cus_details, pr_details, productID, product_title, qty, product_type, product_price, product_img, razorpay_details, order_placed_date, order_placed_date], async function (productError) {
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
                
        
        res.json({ message: 'New Order created successfully', email_data: email_data });
        
              
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
        const { payment_status, payment_type, shipping_charges, cod_charges, amount, payment_mode, customer_type, customer_name, customer_email, customer_contact, delivery_status, customer_address, product_details, productswithqty, razorpay_details } = req.body;       
        // Get current date and time in Kolkata timezone
        const order_placed_date = moment().tz('Asia/Kolkata').format('YYYY-MM-DD HH:mm:ss');
        const cus_details = JSON.stringify(customer_address)
        const pr_details = JSON.stringify(product_details);

        // Loop through productswithqty array and insert order details for each product
        let order_ids = [];
        
        const invoice_number = 'Invoice_EB_'+generateOrderId();

        for (const product of productswithqty) {
            // Generate unique order ID

            const order_id = 'order_'+generateOrderId();
            order_ids.push(order_id);
            
            // console.log(product)

            const { attributes, productID, product_title, qty, product_type, product_price, product_img, variables_quantity } = product;
                        
            await pool.query('INSERT INTO order_details (order_id, invoice_number, payment_status, payment_type, shipping_charges, cod_charges, amount, payment_mode,  customer_type, customer_name, customer_email, customer_contact, delivery_status, customer_address, product_details, product_id, product_title, product_qty,product_type, product_price, product_img, razorpay_details, order_placed_date, ordered_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [order_id, invoice_number, payment_status, payment_type, shipping_charges, cod_charges, amount, payment_mode, customer_type, customer_name, customer_email, customer_contact, delivery_status, cus_details, pr_details, productID, product_title, qty, product_type, product_price, product_img, razorpay_details, order_placed_date, order_placed_date], async function (productError) {
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
                
                // if(customer_type === 'guest' ){
                //     if(product_type === 'simple'){
                //         await pool.query('DELETE FROM cart_products WHERE productID = ? and customerID = ?', [productID,customer_id]);
                //     }else if(product_type === 'variable'){
                //         await pool.query('DELETE FROM cart_products WHERE productID = ? and customerID = ? and attributes = ?', [productID,customer_id,attributes]);
                //     }
                // }
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
//         const response = await axios.post('http://3.7.47.11/backnew/adminnew/src/emailTemplates/order_confirm_customer.php', req.body);
        
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
//         const response = await axios.post('http://3.7.47.11/backnew/adminnew/src/emailTemplates/Admin_NewOrder.php', req.body);
        
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
        console.log(req.body)
        const { userType, userEmail, adminEmail, vendorEmail, orderId, status } = req.body;

        if(status === 'Confirmed'){
            //console.log("confirmed")
            let updatedstatus = 1;
            let delivery_status = 'confirmed';
            const confirmed_date = moment().tz('Asia/Kolkata').format('YYYY-MM-DD HH:mm:ss');
                await pool.query(
                    'UPDATE order_details SET confirmed_status = ?,confirmed_date = ?, delivery_status= ? WHERE order_id = ?',
                    // [name, hashedPassword, email, mobile, user_type, status, id],
                    [updatedstatus, confirmed_date, delivery_status, orderId], function (error, result) {
                        if (error) {
                            console.error(error.message);
                            return res.status(500).json({ message: 'Internal server error' });
                        }
                        axios.post('http://3.7.47.11/backnew/adminnew/src/emailTemplates/order_processing.php', req.body)
                        .then(response => {
                            const htmlTemplate = response.data;
                            console.log(htmlTemplate); // Check if the response is not empty
                           

                            const subject = 'Your Order Is Being Processed - ' + orderId;
                            const email1 = userEmail;
                            // // Send email using nodemailer transporter
                            transporter.sendMail(mailOptions(email1, subject, htmlTemplate), function (error, info) {
                                if (error) {
                                    console.error(error);
                                    return res.status(500).json({ message: 'Failed to send email' });
                                } else {
                                    console.log('Email sent:', info.response);
                                    return res.status(200).json({ message: 'Your Order is Confirmed.' });
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
                'UPDATE order_details SET shipped_status = ?,shipped_date = ?, delivery_status= ? WHERE order_id = ?',
                // [name, hashedPassword, email, mobile, user_type, status, id],
                [updatedstatus, shipped_date, delivery_status, orderId], function (error, result) {
                    if (error) {
                        console.error(error.message);
                        return res.status(500).json({ message: 'Internal server error' });
                    }
                    
                    axios.post('http://3.7.47.11/backnew/adminnew/src/emailTemplates/order_shipped.php', req.body)
                            .then(response => {
                                const htmlTemplate = response.data;
                                //console.log(htmlTemplate); // Check if the response is not empty
                                
                                const subject = 'Your Order Is on Its Way - ' + orderId;
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
                                return axios.post('http://3.7.47.11/backnew/adminnew/src/emailTemplates/Admin-Shipping.php', req.body)
                                        .then((response1) => {

                                            const htmlTemplate = response1.data;

                                            const subject = 'Order Shipped - ' + orderId;
                                            // Send email using nodemailer transporter
                                            transporter.sendMail(mailOptions(adminEmail, subject, htmlTemplate), function (error, info) {
                                                if (error) {
                                                    console.error(error);
                                                    return res.status(200).json({ message: 'Failed to send email' });
                                                } else {
                                                    console.log('Email sent:', info.response);
                                                    return res.status(200).json({ message: 'Your Order is Shipped.' });
                                                }
                                            });
                                        });
                                // if(userType === 'ADMIN'){
                                //     return axios.post('http://3.7.47.11/backnew/adminnew/src/emailTemplates/Admin-Shipping.php', req.body)
                                //         .then((response1) => {

                                //             const htmlTemplate = response1.data;

                                //             const subject = 'Order Shipped - ' + orderId;
                                //             // Send email using nodemailer transporter
                                //             transporter.sendMail(mailOptions(adminEmail, subject, htmlTemplate), function (error, info) {
                                //                 if (error) {
                                //                     console.error(error);
                                //                     return res.status(200).json({ message: 'Failed to send email' });
                                //                 } else {
                                //                     console.log('Email sent:', info.response);
                                //                     return res.status(200).json({ message: 'Your Order is Shipped.' });
                                //                 }
                                //             });
                                //         });
                                // }else if(userType === 'VENDOR'){
                                //     return axios.post('http://3.7.47.11/backnew/adminnew/src/emailTemplates/Vendor-Shipping.php', req.body)
                                //         .then((response1) => {

                                //             const htmlTemplate = response1.data;

                                //             const subject = 'Order Shipped - ' + orderId;
                                //             const email1 = userEmail;
                                //             // Send email using nodemailer transporter
                                //             transporter.sendMail(mailOptions(email1, subject, htmlTemplate), function (error, info) {
                                //                 if (error) {
                                //                     console.error(error);
                                //                     return res.status(500).json({ message: 'Failed to send email' });
                                //                 } else {
                                //                     console.log('Email sent:', info.response);
                                //                     return res.status(200).json({ message: 'Your Order is Shipped.' });
                                //                 }
                                //             });
                                //         });
                                // }
                                
                            })
                            .then(response => {
                                // Handle the response of the third request if needed
                                return res.status(200).json({ message: 'Your Order is Shipped.' });
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
                'UPDATE order_details SET delivered_status = ?,delivered_date = ?, delivery_status= ? WHERE order_id = ?',
                // [name, hashedPassword, email, mobile, user_type, status, id],
                [updatedstatus, delivered_date, delivery_status, orderId], function (error, result) {
                    if (error) {
                        console.error(error.message);
                        return res.status(500).json({ message: 'Internal server error' });
                    }
                    
                    axios.post('http://3.7.47.11/backnew/adminnew/src/emailTemplates/order_delivered.php', req.body)
                            .then(response => {
                                const htmlTemplate = response.data;
                                //console.log(htmlTemplate); // Check if the response is not empty
                                
                                const subject = 'Your Order Has Been Delivered - ' + orderId;
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
                                return axios.post('http://3.7.47.11/backnew/adminnew/src/emailTemplates/Admin-OrderCompleted.php', req.body)
                                    .then((response1) => {

                                        const htmlTemplate = response1.data;

                                        const subject = 'Order Completed - ' + orderId;
                                        // const email1 = userEmail;
                                        // Send email using nodemailer transporter
                                        transporter.sendMail(mailOptions(adminEmail, subject, htmlTemplate), function (error, info) {
                                            if (error) {
                                                console.error(error);
                                                return res.status(500).json({ message: 'Failed to send email' });
                                            } else {
                                                console.log('Email sent:', info.response);
                                                return res.status(200).json({ message: 'Your Order is Completed.' });
                                            }
                                        });
                                    });
                                // if(userType === 'ADMIN'){
                                //     return axios.post('http://3.7.47.11/backnew/adminnew/src/emailTemplates/Admin-OrderCompleted.php', req.body)
                                //         .then((response1) => {

                                //             const htmlTemplate = response1.data;

                                //             const subject = 'Order Completed - ' + orderId;
                                //             const email1 = userEmail;
                                //             // Send email using nodemailer transporter
                                //             transporter.sendMail(mailOptions(adminEmail, subject, htmlTemplate), function (error, info) {
                                //                 if (error) {
                                //                     console.error(error);
                                //                     return res.status(500).json({ message: 'Failed to send email' });
                                //                 } else {
                                //                     console.log('Email sent:', info.response);
                                //                     return res.status(200).json({ message: 'Your Order is Completed.' });
                                //                 }
                                //             });
                                //         });
                                // }
                                // else if(userType === 'VENDOR'){
                                //     return axios.post('http://3.7.47.11/backnew/adminnew/src/emailTemplates/Vendor-OrderCompleted.php', req.body)
                                //         .then((response1) => {

                                //             const htmlTemplate = response1.data;

                                //             const subject = 'Order Completed - ' + orderId;
                                //             const email1 = userEmail;
                                //             // Send email using nodemailer transporter
                                //             transporter.sendMail(mailOptions(email1, subject, htmlTemplate), function (error, info) {
                                //                 if (error) {
                                //                     console.error(error);
                                //                     return res.status(500).json({ message: 'Failed to send email' });
                                //                 } else {
                                //                     console.log('Email sent:', info.response);
                                //                     return res.status(200).json({ message: 'Your Order is Completed.' });
                                //                 }
                                //             });
                                //         });
                                // }
                                
                            })
                            .then(response => {
                                // Handle the response of the third request if needed
                                return res.status(200).json({ message: 'Your Order is Completed.' });
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
//         const response = await axios.post('http://3.7.47.11/backnew/adminnew/src/emailTemplates/order_confirm_customer_all.php', record);
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
//         const response1 = await axios.post('http://3.7.47.11/backnew/adminnew/src/emailTemplates/Admin_NewOrder.php', record);
        
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

// // Call cronjobsdata every 5 seconds
// // setInterval(cronjobsdata, 5000);



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
        const result = await queryAsync('SELECT distinct invoice_number,customer_address,customer_name,customer_email FROM order_details WHERE delivery_status = ? AND processed = ?', [delivery_status, false]);

        if (result.length === 0) {
            console.log('No pending orders found.');
            return;
        }

        for (const row of result) {
            await sendOrderConfirmationEmail(row);

            // Update delivery_status
            await updateDeliveryStatus(row.invoice_number);
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

const updateDeliveryStatus = async (invoiceNumber) => {
    try {
        const result = await queryAsync('UPDATE order_details SET delivery_status = ?, processed = ? WHERE invoice_number = ?', ['ordered', true, invoiceNumber]);
        console.log(`Delivery status updated for invoice number: ${invoiceNumber}`);
        console.log(result); // Log the result of the update query
    } catch (error) {
        console.error('Failed to update delivery status:', error.message);
        throw new Error('Failed to update delivery status');
    }
};


const sendOrderConfirmationEmail = async (record) => {
    try {
        const response = await axios.post('http://3.7.47.11/backnew/adminnew/src/emailTemplates/order_confirm_customer_all.php', record);
        const htmlTemplate = response.data;
        const subject = 'Voila! Your Order is Confirmed - ' + record.invoice_number;

        await sendEmail(record.customer_email, subject, htmlTemplate);

        const adminTemplateResponse = await axios.post('http://3.7.47.11/backnew/adminnew/src/emailTemplates/Admin_NewOrder.php', record);
        const adminHtmlTemplate = adminTemplateResponse.data;
        const adminSubject = 'New Order Placed - ' + record.invoice_number;
        const adminEmail = 'hello@earthbased.store'; // Change this to your admin email address

        await sendEmail(adminEmail, adminSubject, adminHtmlTemplate);
    } catch (error) {
        console.error('Failed to send email:', error.message);
    }
};

// Call cronjobsdata every 5 seconds
setInterval(cronjobsdata, 5000);


const cronjobsdatanew = async () => {
    console.log("Cronjob running for emails");

    try {
        const delivery_status = 'testing';
        const result = await queryAsync('SELECT distinct invoice_number,customer_address,customer_name,customer_email FROM order_details WHERE delivery_status = ?', [delivery_status]);

        if (result.length === 0) {
            console.log('No pending orders found.');
            return;
        }

        for (const row of result) {
            await sendOrderConfirmationEmailwithAttachment(row);

            // Update delivery_status
            // await updateDeliveryStatus(row.invoice_number);
        }

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


const sendEmailAttachment = async (email, subject, htmlTemplate, file_name, filepath) => {
    try {
        await transporter.sendMail(mailOptionswithAttachment(email, subject, htmlTemplate, file_name, filepath));
        console.log('Email sent to', email);
    } catch (error) {
        console.error('Failed to send email:', error);
        throw new Error('Failed to send email');
    }
};

const sendOrderConfirmationEmailwithAttachment = async (record) => {
    try {
        const response = await axios.post('http://3.7.47.11/backnew/adminnew/src/emailTemplates/order_confirm_customer_all.php', record);
        const htmlTemplate = response.data;
        const subject = 'Voila! Your Order is Confirmed - ' + record.invoice_number;

        const response1 = await axios.post('http://3.7.47.11/backnew/adminnew/src/emailTemplates/generate_invoice.php', htmlTemplate);
        const htmlTemplate2 = response1.data;

        console.log(htmlTemplate2)

        //await sendEmailAttachment(record.customer_email, subject, htmlTemplate, file_name, file_path);

        // const adminTemplateResponse = await axios.post('http://3.7.47.11/backnew/adminnew/src/emailTemplates/Admin_NewOrder.php', record);
        // const adminHtmlTemplate = adminTemplateResponse.data;
        // const adminSubject = 'New Order Placed - ' + record.invoice_number;
        // const adminEmail = 'hello@earthbased.store'; // Change this to your admin email address

        // await sendEmail(adminEmail, adminSubject, adminHtmlTemplate);
    } catch (error) {
        console.error('Failed to send email:', error.message);
    }
};

cronjobsdatanew()
// end cronjob mails functionality


module.exports = router;
