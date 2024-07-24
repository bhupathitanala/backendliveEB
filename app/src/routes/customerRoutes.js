const express = require('express');
const router = express.Router();
const bcrypt = require('bcrypt');
const pool = require('../database');
const nodemailer = require('nodemailer');
const moment = require('moment-timezone'); // Import moment-timezone library
const axios = require('axios');

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


// Function to read HTML template file
// const readHTMLTemplate = (filename) => {
//     return new Promise((resolve, reject) => {
//         const filePath = path.join(__dirname, filename);
//         fs.readFile(filePath, 'utf8', (err, data) => {
//             if (err) {
//                 reject(err);
//             } else {
//                 resolve(data);
//             }
//         });
//     });
// };


const checkCustomerExists = async (email) => {
    return new Promise((resolve, reject) => {
        pool.query('SELECT * FROM customer WHERE email = ?', [email], function (error, result) {
            if (error) {
                reject(error);
            } else {
                resolve(result.length > 0);
            }
        });
    });
};

const generateOTP = () => {
    const min = 100000;
    const max = 999999;
    const randomNum = Math.floor(Math.random() * (max - min + 1)) + min;
    return randomNum.toString();
};

// Create a new customer
router.post('/', async (req, res) => {
    try {
        const { email, password, name, mobile, status } = req.body;
        const missingFields = {};
        if (!email) missingFields.email = 'Email';
        if (!password) missingFields.password = 'Password';
        if (!name) missingFields.name = 'Name';
        if (!mobile) missingFields.mobile = 'Mobile';
        if (!status) missingFields.status = 'Status';

        if (Object.keys(missingFields).length > 0) {
            return res.status(400).json({ message: 'Missing required fields', missingFields });
        }

        const customerExists = await checkCustomerExists(email);
        if (customerExists) {
            return res.status(400).json({ message: 'User already exist' });
        }

        const hashedPassword = await bcrypt.hash(password, 10);

        await pool.query('INSERT INTO customer (email, password, fullName, phoneNumber, status) VALUES (?, ?, ?, ?, ?)',
            [email, hashedPassword, name, mobile, status], async function (error, result) {
                if (error) {
                    console.error(error.message);
                    return res.status(500).json({ message: 'Internal server error' });
                }
                const insertedId = result.insertId;
                await pool.query('SELECT * FROM customer WHERE id = ?', [insertedId], function (error, data) {
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
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});

// Get all customer items
router.get('/', async (req, res) => {
    try {
        await pool.query('SELECT * FROM customer', function (error, result, fields) {
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

// Get a customer by ID
router.get('/:id', async (req, res) => {
    try {
        const { id } = req.params;
        await pool.query('SELECT * FROM customer WHERE id = ?', [id], function (error, data) {
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

// Update a customer
router.put('/:id', async (req, res) => {
    try {
        const { id } = req.params;
        const { email,
            // password,
            name, mobile, address, status } = req.body;
        const missingFields = {};
        if (!email) missingFields.email = 'Email';
        if (!address) missingFields.address = 'Address';
        // if (!password) missingFields.password = 'Password';
        if (!name) missingFields.name = 'Name';
        if (!mobile) missingFields.mobile = 'Mobile';
        if (!status) missingFields.status = 'Status';

        if (Object.keys(missingFields).length > 0) {
            return res.status(400).json({ message: 'Missing required fields', missingFields });
        }

        // const hashedPassword = await bcrypt.hash(password, 10);

        await pool.query(
            // 'UPDATE customer SET name = ?, password = ?, email = ?, mobile = ?, user_type = ?, status = ? WHERE id = ?',
            'UPDATE customer SET fullName = ?,  email = ?, phoneNumber = ?, status = ? WHERE id = ?',
            // [name, hashedPassword, email, mobile, user_type, status, id],
            [name, email, mobile, status, id],
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
            // 'UPDATE customer SET name = ?, password = ?, email = ?, mobile = ?, user_type = ?, status = ? WHERE id = ?',
            'UPDATE customer SET status = ? WHERE id = ?',
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

// Delete a customer
router.delete('/:id', async (req, res) => {
    try {
        const { id } = req.params;

        await pool.query('DELETE FROM customer WHERE id = ?', [id], function (error, result) {
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

// Self Login route
router.post('/login', async (req, res) => {
    try {
        const { email, password } = req.body;

        // Fetch the user record based on the provided email
        pool.query('SELECT * FROM customer WHERE email = ?', [email], async (queryError, queryResult) => {
            if (queryError) {
                console.error(queryError.message);
                return res.status(500).json({ message: 'Internal server error' });
            }

            const user = queryResult[0];

            if (!user) {
                return res.status(404).json({ message: 'User not found' });
            }

            // Compare the provided password with the hashed password stored in the database
            const passwordMatch = await bcrypt.compare(password, user.password);

            if (passwordMatch) {
                // Passwords match, login successful
                res.json({ message: 'Login successful', user: user });
            } else {
                // Passwords don't match, return error
                res.status(401).json({ message: 'Invalid credentials' });
            }
        });

    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});


// // Google Login route
// router.post('/googleLogin', async (req, res) => {
//     try {
//         const { email, name } = req.body;

//         // Fetch the user record based on the provided email
//         pool.query('SELECT * FROM customer WHERE email = ?', [email], async (queryError, queryResult) => {
//             if (queryError) {
//                 console.error(queryError.message);
//                 return res.status(500).json({ message: 'Internal server error' });
//             }

//             const user = queryResult[0];

//             if (!user) {
//                 await pool.query('INSERT INTO customer (email, fullName, phoneNumber, password, type, status) VALUES (?, ?, ?, ?, ?, ?)',
//                 [email, name, '1234567890', 'a7sd9ayv9a6ts9vaysdbgkbuu', 'google', '1'], async function (error, result) {
//                     if (error) {
//                         console.error(error.message);
//                         return res.status(500).json({ message: 'Internal server error' });
//                     }
//                     const insertedId = result.insertId;

//                     await pool.query('SELECT * FROM customer WHERE id = ?', [insertedId], function (error, data) {
//                         if (error) {
//                             console.error(error.message);
//                             return res.status(500).json({ message: 'Internal server error' });
//                         }
//                         // if (data.length === 0) {
//                         //     return res.status(404).json({ message: 'Customer not found' });
//                         // }
//                         res.json({ message: 'Login successful', user: data[0] });
//                     });
//                 });
//             }
//             res.json({ message: 'Login successful', user: user });
//         });

//     } catch (error) {
//         console.error(error.message);
//         res.status(500).json({ message: 'Internal server error' });
//     }
// });


// Google Login route
router.post('/googleLogin', async (req, res) => {
    try {
        const { email, name } = req.body;

        // Check if the user already exists in the database
        const existingUser = await getUserByEmail(email);

        if (existingUser) {
            // User exists, return the user data
            return res.json({ message: 'Login successful', user: existingUser });
        } else {
            // User does not exist, create a new user
            const insertedUser = await insertGoogleUser(email, name);

            if (insertedUser) {
                // User created successfully, return the user data
                return res.json({ message: 'Login successful', user: insertedUser });
            } else {
                // Failed to insert user (unlikely case)
                return res.status(500).json({ message: 'Failed to insert user' });
            }
        }

    } catch (error) {
        console.error('Error in /googleLogin:', error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});

// Function to get user by email
async function getUserByEmail(email) {
    try {
        const queryResult = await poolQuery('SELECT * FROM customer WHERE email = ?', [email]);
        return queryResult[0] || null; // Return the first result or null if not found
    } catch (error) {
        console.error('Error in getUserByEmail:', error.message);
        throw error;
    }
}

// Function to insert a new Google user
async function insertGoogleUser(email, name) {
    try {
        const result = await poolQuery('INSERT INTO customer (email, fullName, phoneNumber, password, type, status) VALUES (?, ?, ?, ?, ?, ?)',
            [email, name, '', 'sa87sd897ad89v7gads9g', 'google', '1']);

        const insertedId = result.insertId;

        // Fetch the inserted user data
        const userData = await poolQuery('SELECT * FROM customer WHERE id = ?', [insertedId]);
        return userData[0] || null; // Return the first result or null if not found
    } catch (error) {
        console.error('Error in insertGoogleUser:', error.message);
        throw error;
    }
}

// Helper function to execute pool.query with error handling
async function poolQuery(sql, values = []) {
    return new Promise((resolve, reject) => {
        pool.query(sql, values, (error, result) => {
            if (error) {
                reject(error);
            } else {
                resolve(result);
            }
        });
    });
}


// Forgot route
router.post('/forgotpwd', async (req, res) => {
    const {email} = req.body;

    const otp = generateOTP();
    const subject = "One Time Password"
    const htmlTemplate = "Your One Time Password is <b>" + otp + "</b>";
    const current_date = moment().tz('Asia/Kolkata').format('YYYY-MM-DD HH:mm:ss');
    
    pool.query('SELECT * FROM customer WHERE email = ?', [email], async (queryError, queryResult) => {
        if (queryError) {
            console.error(queryError.message);
            return res.status(500).json({ message: 'Internal server error' });
        }

        const user = queryResult[0];
        
        if (!user) {
            return res.status(404).json({ message: 'User not found' });
        }

        await pool.query('INSERT INTO customer_otps (user_id, otp, date) VALUES(?, ?, ?)', [user.ID, otp, current_date], function (error, result) {
            if (error) {
                console.error(error.message);
                return res.status(500).json({ message: 'Internal server error' });
            }
            
            
            // Send email using nodemailer transporter
            transporter.sendMail(mailOptions(email, subject, htmlTemplate), function (error, info) {
                if (error) {
                    console.error(error);
                    return res.status(500).json({ message: 'Failed to send email' });
                } else {
                    console.log('Email sent:', info.response);
                    return res.status(200).json({ message: 'Your Order is Completed.' });
                }
            });
            return res.json({ message: 'Mail Sent' });
        })
        
        // return res.status(200).json({ message:  user.ID, otp: otp});
    })
});


router.post("/verifyOTP", async (req, res) => {
    const {email, otp} = req.body;
    const today_date = moment().tz('Asia/Kolkata').format('YYYY-MM-DD');

    pool.query('SELECT * FROM customer WHERE email = ?', [email], async (queryError, queryResult) => {
        if (queryError) {
            console.error(queryError.message);
            return res.status(500).json({ message: 'Internal server error' });
        }

        const user = queryResult[0];
        
        if (!user) {
            return res.status(404).json({ message: 'User not found' });
        }

        pool.query('SELECT * FROM customer_otps WHERE user_id= ? and date(date)= ? ORDER BY id DESC', [user.ID, today_date], async(error, result) => {
            if (error) {
                console.error(error);
                return res.status(500).json({ message: 'Internal server error' });
            } else {
                if(otp === result[0].otp){
                    pool.query('DELETE FROM customer_otps WHERE user_id= ?', [user.ID], async(error, result) => {
                        if (error) {
                            console.error(error);
                        }
                    })
                    return res.status(200).json({ message: 'OTP Verified' });
                }else{
                    return res.status(500).json({ message: 'Invali OTP' });
                }
            }
        })
    })

})




router.post("/changePassword", async (req, res) => {
    const {email, password, cpassword} = req.body;

    if(password !== cpassword){
        return res.status(500).json({ message: "Password Mismatch" });
    }

    const hashedPassword = await bcrypt.hash(password, 10);


    pool.query('UPDATE customer SET password= ? WHERE email = ?', [hashedPassword, email], async (queryError, queryResult) => {
        if (queryError) {
            console.error(queryError.message);
            return res.status(500).json({ message: 'Internal server error' });
        }else{
            return res.status(200).json({ message: 'Password Changed' });
        }
    })

})

// Mail sent for customer details to Earthbased support team

router.post('/contactmail', async (req, res) => {
    try {
        // const { email, fullname, message } = req.body;
        //console.log(req.body)

        // Fetch HTML template from the PHP file
        const response = await axios.post('http://3.7.47.11/backnew/adminnew/src/emailTemplates/contact.php', req.body);
        
        // Extract HTML content from the response
        const htmlTemplate = response.data;

        const subject = 'Customer Contact Details';
        const admin_email = 'hello@earthbased.store';
        // Send email using nodemailer transporter
        transporter.sendMail(mailOptions(admin_email, subject, htmlTemplate), function (error, info) {
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
