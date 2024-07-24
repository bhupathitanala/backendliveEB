
const jwt = require('jsonwebtoken');
const cors = require('cors');
const uniqueid = process.env.ebcode;

// const verifyToken = (req, res, next) => {
//     // Get token from headers, query parameters, cookies, or wherever you're sending it
//     // const token = req.headers.authorization;
//     const secretval = process.env.secretkey;
//     // const authHeader = req.headers['authorization'];
//     // const token = authHeader && authHeader.split(' ')[1];
//     const token = req.cookies.token;
//     // console.log("token",token);

//     if (!token) {
//         return res.status(401).json({ message: 'Unauthorized: Missing token' });
//     }

//     // Verify JWT token
//     jwt.verify(token, secretval, (err, decoded) => {
//         if (err) {
//           console.log('Unauthorized: Invalid token')
//             return res.status(401).json({ message: 'Unauthorized: Invalid token' });
//         }
//         // If valid token, attach decoded data to request object
//         req.user = decoded;
//         next(); // Pass to next middleware or route handler
//     });
// };

const verifyToken = (req, res, next) => {
    /**********************
     * ********************
     * First verify User Agent
     * ********************
     * ********************/
    // const userAgent = req.get('User-Agent');
    // if (!userAgent || !userAgent.includes('Mozilla')) {
    //     return res.status(403).json({});
    // }
    const userAgent = req.get('User-Agent');

    // Define an array of allowed user agents or substrings to check against
    const allowedUserAgents = ['Mozilla', 'Chrome', 'Safari'];

    // Check if userAgent exists and if it includes any of the allowed user agents
    if (!userAgent || !allowedUserAgents.some(ua => userAgent.includes(ua))) {
        return res.status(403).json({ error: 'Forbidden' });
    }

    /**********************
     * ********************
     * CORS
     * ********************
     * ********************/
    // List of allowed origins
    // const allowedOrigins = [
    //     'https://earthbased.store'
    // ];  
    // const corsOptions = {
    //     origin: function (origin, callback) {
    //         if (!origin || allowedOrigins.includes(origin)) {
    //             callback(null, true);
    //         } else {
    //             callback(new Error('Not allowed by CORS'));
    //         }
    //     }
    // };
    // // Use CORS middleware with the specified options
    // cors(corsOptions)(req, res, (err) => {
    //     if (err) {
    //         return res.status(403).json({ error: 'Not allowed by CORS' });
    //     }
    // });

    // Get token from headers, query parameters, cookies, or wherever you're sending it
    // const token = req.headers.authorization;
    const secretval = process.env.secretkey;
    const authHeader = req.headers['authorization'];
    const token = authHeader && authHeader.split(' ')[1];
    // const token = req.cookies.token;
    // console.log("token",token);

    if (!token) {
        return res.status(401).json({ message: 'Unauthorized: Missing token' });
    }

    // Verify JWT token
    jwt.verify(token, secretval, {audience:'EBKKD', algorithms:['HS256']}, (err, decoded) => {
        if (err) {
          console.log('Unauthorized: Invalid token')
            return res.status(401).json({ message: 'Unauthorized: Invalid token' });
        }
        // If valid token, attach decoded data to request object
        req.user = decoded;
        next(); // Pass to next middleware or route handler
    });
};

module.exports = verifyToken;
