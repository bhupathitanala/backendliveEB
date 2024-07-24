const express = require('express');
const router = express.Router();
const pool = require('../database');


// // Create a new category
// router.get('/',  async (req, res) => {
//     try {
//         const { title } = req.query;
//         let titleQuery = title ? `WHERE productsnew.title LIKE '%${title}%' OR categories.categoryName LIKE '%${title}%' OR subcategory.subcategoryName LIKE '%${title}%'` : '';
//         const query = `
//             Select productsnew.ID as productId, categories.ID as mcID, subcategory.ID as scID, productsnew.title, categories.categoryName,subcategory.subcategoryName from productsnew
//             JOIN categories as categories ON productsnew.category = categories.ID
//             JOIN subcategory as subcategory ON categories.ID = subcategory.mcID
//             ${titleQuery} limit 10;
//         `;
//         console.log(query)
//         await pool.query(query, async function (error, result) {
//                 if (error) {
//                     console.error(error.message);
//                     res.status(500).json({ message: 'Internal server error' });
//                 } else {
//                     res.json(result);
//                 }
//             });
//     } catch (error) {
//         console.error(error.message);
//         res.status(500).json({ message: 'Internal server error' });
//     }
// });


// Create a new category
router.get('/',  async (req, res) => {
    try {
        const { title } = req.query;
        // let titleQuery = title ? `WHERE productsnew.title LIKE '%${title}%' OR productsnew.tags LIKE '%${title}%' OR categories.categoryName LIKE '%${title}%' OR subcategory.subcategoryName LIKE '%${title}%'` : '';
        // const query = `
        //     Select productsnew.ID as productId, categories.ID as mcID, subcategory.ID as scID, productsnew.title, categories.categoryName,subcategory.subcategoryName from productsnew
        //     JOIN categories as categories ON productsnew.category = categories.ID
        //     JOIN subcategory as subcategory ON categories.ID = subcategory.mcID
        //     ${titleQuery} limit 10;
        // `;
        // let titleQuery = title ? `(productsnew.title LIKE '%${title}%' OR productsnew.tags LIKE '%${title}%') AND` : '';
        let titleQuery = title ? `(productsnew.title LIKE '%${title}%' OR productsnew.tags LIKE '%${title}%') AND` : '';
        const query = `
            Select DISTINCT productsnew.ID as productId, productsnew.title, productsnew.product_type from productsnew where ${titleQuery}  productsnew.status = 1 limit 10;
        `;
        console.log(query)
        await pool.query(query, async function (error, result) {
                if (error) {
                    console.error(error.message);
                    res.status(500).json({ message: 'Internal server error' });
                } else {
                    res.json(result);
                }
            });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});


router.get('/filterdata',  async (req, res) => {
    try {
        const title = req.query.title;
        // console.log(title)

        
        let titleQuery = title ? `WHERE productsnew.title LIKE '%${title}%' OR productsnew.tags LIKE '%${title}%'` : '';
        // let titleQuery = title ? `WHERE (productsnew.title LIKE '${title}%' OR productsnew.title LIKE ' ${title}%') OR (productsnew.tags LIKE '${title}%' OR productsnew.tags LIKE ' ${title}%')` : '';
        // let titleQuery = title ? `WHERE (productsnew.title LIKE '${title}%') OR (productsnew.tags LIKE '${title}%' OR productsnew.tags LIKE ' ${title}%')` : '';
        
        //select * from productsnew where ID in (Select DISTINCT ID from productsnew WHERE title LIKE '%s%' OR tags LIKE '%s%') limit 100;
        const query = `select a.*,b.name as vendorName from productsnew a join vendors b on b.vendorID = a.vendor_id where a.ID in (Select DISTINCT ID from productsnew ${titleQuery}) AND a.status = 1 limit 50`;
        // console.log(query);

        await pool.query(query, async function (error, result) {
                if (error) {
                    console.error(error.message);
                    res.status(500).json({ message: 'Internal server error' });
                } else {
                    res.json(result);
                }
            });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});

module.exports = router;