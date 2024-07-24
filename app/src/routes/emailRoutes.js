// Import nodemailer
const express = require('express');
const router = express.Router();
const pool = require('../database');
const fs = require('fs');
const path = require('path');
const multer = require('multer')
const nodemailer = require('nodemailer');
const axios = require('axios');

// // Create a transporter
// const transporter = nodemailer.createTransport({
//     service: 'gmail',
//     auth: {
//         user: 'koyiladavignesh@gmail.com',
//         pass: 'bofigeusuhftespu'
//     }
// });

// // Define email options
// function mailOptions(email) {
//     return {
//         from: 'earthbased@gmail.com',
//         to: email,
//         subject: 'Mail from earth based store',
//         text: 'Helo User'
//     }
// };



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
const mailOptions = (toEmail, template) => {
    let fromName = 'EarthBased';
    let fromEmail = 'hello@earthbased.store';
    return {
        from: `"${fromName}" <${fromEmail}>`, // Sender address
        to: toEmail, // List of recipients
        subject: 'Test Email', // Subject line
        html: template // Use the HTML content from the template file
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

// Define endpoint to send test email
// router.get('/testingmail', async (req, res) => {
//     try {
//         const htmlTemplate = await readHTMLTemplate('emailTemplates/order_delivery.html');
//         const email = 'simhapune@gmail.com'; // Recipient email
//         transporter.sendMail(mailOptions(email, htmlTemplate), function (error, info) {
//             if (error) {
//                 console.error(error);
//                 res.status(500).json({ message: 'Failed to send email' });
//             } else {
//                 console.log('Email sent:', info.response);
//                 res.status(200).json({ message: 'Email sent successfully' });
//             }
//         });
//     } catch (error) {
//         console.error(error.message);
//         res.status(500).json({ message: 'Internal server error' });
//     }
// });








router.post('/ordermeails',async (req, res) => {
    try {
        const { id, name, email, message } = req.body;
        
        await pool.query(
            // 'UPDATE orders SET name = ?, password = ?, email = ?, mobile = ?, user_type = ?, status = ? WHERE id = ?',
            'UPDATE order_details SET name = ?, email = ?, message = ? WHERE id = ?',
            // [name, hashedPassword, email, mobile, user_type, status, id],
            [name, email, message, id],
            async function (error, result) {
                // const htmlTemplate = await readHTMLTemplate('emailTemplates/order_delivery.html');
                // transporter.sendMail(mailOptions(email, htmlTemplate), function (error, info) {
                //     if (error) {
                //         console.error(error);
                //         res.status(500).json({ message: 'Failed to send email' });
                //     } else {
                //         console.log('Email sent:', info.response);
                //         res.status(200).json({ message: 'Email sent successfully' });
                //     }
                // });
                if (error) {
                    console.error(error.message);
                    return res.status(500).json({ message: 'Internal server error' });
                }

                res.json({ message: 'Enquiry updated successfully' });
            }
        );
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }

})


// Define endpoint to send test email
router.get('/testingmail', async (req, res) => {
    try {
        await axios.get('../../../adminnew/src/emailTemplates/order_delivered.php')
        .then(response => {
            const htmlTemplate = response.data; // Assuming the PHP file returns HTML content
            const email = 'simhapune@gmail.com';
            transporter.sendMail(mailOptions(email, htmlTemplate), function (error, info) {
                if (error) {
                    console.error(error);
                    res.status(500).json({ message: 'Failed to send email' });
                } else {
                    console.log('Email sent:', info.response);
                    res.status(200).json({ message: 'Email sent successfully' });
                }
            });
        })
        .catch(error => {
            console.error(error);
            res.status(500).json({ message: 'Failed to fetch HTML template from PHP file' });
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});

router.post('/sendgmail', async (req, res) => {
    try {
        const { email } = req.body;
        let orders = {
            "id": 1234,
            "customer_name":"sim"
        }

        // Fetch HTML template from the PHP file
        const response = await axios.post('http://65.1.233.19/backnew/adminnew/src/emailTemplates/order_delivered.php', orders);

        // Extract HTML content from the response
        const htmlTemplate = response.data;

        // Send email using nodemailer transporter
        transporter.sendMail(mailOptions(email, htmlTemplate), function (error, info) {
            if (error) {
                console.error(error);
                return res.status(500).json({ message: 'Failed to send email' });
            } else {
                console.log('Email sent:', info.response);
                return res.status(200).json({ message: 'Email sent successfully' });
            }
        });
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



module.exports = router;