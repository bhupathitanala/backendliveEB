const express = require('express');
const router = express.Router();
const bcrypt = require('bcrypt');
const pool = require('../database');

// Get all coupons
router.post('/getdata', async (req, res) => {    
    try {
        const specificProductIds = req.body; // Array of specific product IDs
    
        const query = `
            SELECT DISTINCT c.*
            FROM coupons c
            JOIN productsnew p ON (
                FIND_IN_SET(p.ID, c.products) > 0
                OR FIND_IN_SET(p.category, c.categories) > 0
                OR FIND_IN_SET(p.subcategory, c.subCategories) > 0
                OR FIND_IN_SET(p.brand_id, c.brands) > 0
                OR (c.applied_for = 'All')
            )
            WHERE p.ID IN (?)
            AND c.status = 1;
        `;
    
        await pool.query(query, [specificProductIds], function (error, result, fields) {
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

// Get coupon by given product id and product price
router.post('/getcouponsbyproduct', async (req, res) => {    
    try {
        const {product_id, product_price} = req.body;
        // console.log(product_id)
        const query = `
            SELECT rc.*
                FROM (
                    SELECT c.*, 
                        ROW_NUMBER() OVER (
                            PARTITION BY c.id 
                            ORDER BY 
                                CASE 
                                    WHEN FIND_IN_SET(p.brand_id, c.brands) > 0 THEN 1
                                    WHEN FIND_IN_SET(p.category, c.categories) > 0 THEN 2
                                    WHEN FIND_IN_SET(p.subcategory, c.subCategories) > 0 THEN 3
                                    WHEN FIND_IN_SET(p.ID, c.products) > 0 THEN 4
                                    ELSE 5
                                END
                        ) AS rn
                    FROM coupons c
                    JOIN productsnew p ON (
                        FIND_IN_SET(p.ID, c.products) > 0
                        OR FIND_IN_SET(p.category, c.categories) > 0
                        OR FIND_IN_SET(p.subcategory, c.subCategories) > 0
                        OR FIND_IN_SET(p.brand_id, c.brands) > 0
                    )
                    WHERE p.ID = ?
                    AND c.status = 1
                    AND c.purchaseAmount <= ?
                ) AS rc
                WHERE rn = 1 limit 1
        `;
        // console.log(query)
        await pool.query(query, [parseInt(product_id), parseInt(product_price)], function (error, result) {
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

// Get EB coupon
router.get('/getebcouponsbyproduct', async (req, res) => {
    try {
        await pool.query('SELECT * FROM `coupons` WHERE applied_for = "All" AND status = 1', function (error, result, fields) {
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


// Get Products for coupon available
router.post('/produtcsinfo', async (req, res) => {    
    try {
        const specificProductIds = req.body; // Array of specific product IDs
    
        const query = `SELECT ID,product_id,title,category,subcategory,brand_id,vendor_id FROM productsnew WHERE ID in (?) and status=1`;
    
        await pool.query(query, [specificProductIds], function (error, result, fields) {
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

// Get all coupons
router.get('/list', async (req, res) => {
    try {
        await pool.query('SELECT * FROM `coupons` WHERE status = 1', function (error, result, fields) {
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

// Get a coupon by ID
router.get('/:id', async (req, res) => {
    try {
        const { id } = req.params;
        await pool.query('SELECT * FROM `coupons` WHERE ID = ? and status = 1', [id], function (error, data) {
            if (error) {
                console.error(error.message);
                return res.status(500).json({ message: 'Internal server error' });
            }
            if (data.length === 0) {
                return res.status(404).json({ message: 'Cart not found' });
            }
            res.json(data[0]);
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});


module.exports = router;
