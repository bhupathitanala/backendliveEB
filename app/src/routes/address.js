// Import nodemailer
const express = require('express');
const router = express.Router();
const pool = require('../database');



router.post('/saveaddress', async (req, res) => {
    console.log(req.body)
    try {
        const { firstName, lastName, phone, email, address, pincode, city, state, customer_id } = req.body;
        const missingFields = {};
        if (!firstName) missingFields.firstName = 'FirstName';
        // if (!lastName) missingFields.lastName = 'lastName';
        if (!phone) missingFields.phone = 'phone';
        if (!email) missingFields.email = 'Email';
        if (!address) missingFields.address = 'Address';
        if (!pincode) missingFields.pincode = 'Pincode';
        if (!city) missingFields.city = 'City';
        if (!state) missingFields.state = 'State';

        if (Object.keys(missingFields).length > 0) {
            return res.status(400).json({ message: 'Missing required fields', missingFields });
        }

        await pool.query('INSERT INTO customeraddress (firstName, lastName, phone, email, address, pincode, city, state, customer_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [firstName, lastName, phone, email, address, pincode, city, state, customer_id], async function (error, result) {
                if (error) {
                    console.error(error.message);
                    return res.status(500).json({ message: 'Internal server error' });
                }
                const insertedId = result.insertId;
                await pool.query('SELECT * FROM customeraddress WHERE id = ?', [insertedId], function (error, data) {
                    // Send email
                    // transporter.sendMail(mailOptions(email), function (error, info) {
                    //     if (error) {
                    //         console.log(error);
                    //     } else {
                    //         console.log('Email sent: ' + info.response);
                    //     }
                    // });
                    if (error) {
                        console.error(error.message);
                        return res.status(500).json({ message: 'Internal server error' });
                    }
                    if (data.length === 0) {
                        return res.status(404).json({ message: 'Address not saved' });
                    }
                    res.json(data[0]);
                });
            });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});




router.get('/getaddress/:id', async (req, res) => {
    try {
        const { id } = req.params;
        // const id = '9';
        await pool.query('SELECT * FROM customeraddress WHERE customer_id = ?', [id],  function (error, result) {
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


router.post('/saveguestaddress', async (req, res) => {
    // console.log(req.body)
    try {
        const { firstName, lastName, phone, email, address, pincode, city, state, customer_type } = req.body;
        const missingFields = {};
        if (!firstName) missingFields.firstName = 'FirstName';
        // if (!lastName) missingFields.lastName = 'lastName';
        if (!phone) missingFields.phone = 'phone';
        if (!email) missingFields.email = 'Email';
        if (!address) missingFields.address = 'Address';
        if (!pincode) missingFields.pincode = 'Pincode';
        if (!city) missingFields.city = 'City';
        if (!state) missingFields.state = 'State';

        if (Object.keys(missingFields).length > 0) {
            return res.status(400).json({ message: 'Missing required fields', missingFields });
        }

        

        await pool.query('INSERT INTO customeraddress (firstName, lastName, phone, email, address, pincode, city, state, customer_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [firstName, lastName, phone, email, address, pincode, city, state, customer_type], async function (error, result) {
                if (error) {
                    console.error(error.message);
                    return res.status(500).json({ message: 'Internal server error' });
                }
                const insertedId = result.insertId;
                await pool.query('SELECT * FROM customeraddress WHERE id = ? and customer_type = ?', [insertedId, customer_type], function (error, data) {
                    if (error) {
                        console.error(error.message);
                        return res.status(500).json({ message: 'Internal server error' });
                    }
                    if (data.length === 0) {
                        return res.status(404).json({ message: 'Address not saved' });
                    }
                    res.json(data[0]);
                });
            });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});


module.exports = router;