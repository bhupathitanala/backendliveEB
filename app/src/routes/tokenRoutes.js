const express = require('express');
const router = express.Router();
const bcrypt = require('bcrypt');
const pool = require('../database');
const jwt = require('jsonwebtoken');
const secretval = process.env.secretkey;
const uniqueid = process.env.ebcode;

// Get all Challenges
// router.get('/generateJwttoken', async (req, res) => {
//     // console.log(secretval)
//     // console.log(uniqueid)
//     const accessToken = jwt.sign({id:uniqueid},secretval);
    
//     // Set HttpOnly cookie
//     res.cookie('token', accessToken, {
//         httpOnly: true,
//         secure: true, // Set to true if using HTTPS
//         sameSite: 'Strict', // Adjust based on your needs
//     });
//     // const token = req.cookies.token;
//     // console.log("Coockie Token " + token);
//     res.json({message: 'Token Refreshed Successfully.'});
// });

router.get('/generateJwttoken', async (req, res) => {
    // console.log(secretval)
    // console.log(uniqueid)
    const accessToken = jwt.sign({id:uniqueid, aud: "EBKKD"},secretval, { algorithm:'HS256' });
    
    // Set HttpOnly cookie
    // res.cookie('token', accessToken, {
    //     httpOnly: true,
    //     secure: true, // Set to true if using HTTPS
    //     sameSite: 'Strict', // Adjust based on your needs
    // });
    // const token = req.cookies.token;
    // console.log("Coockie Token " + token);
    res.json(accessToken);
});


module.exports = router;
