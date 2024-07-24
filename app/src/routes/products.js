const express = require('express');
const multer = require('multer');
const path = require('path');
const fs = require('fs');
const router = express.Router();
const pool = require('../database');
const { off } = require('process');

const storage = multer.diskStorage({
    destination: function (req, file, cb) {
        // Specify the destination folder for uploads
        cb(null, 'public/uploads/');
    },
    filename: function (req, file, cb) {
        // Generate unique filename including the field name
        const fieldName = file.fieldname;
        const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
        const ext = path.extname(file.originalname);
        const filename = `${uniqueSuffix}${ext}`;
        // Save the filename in the request object for later use
        req.uploadedFilenames = req.uploadedFilenames || {};
        req.uploadedFilenames[fieldName] = req.uploadedFilenames[fieldName] || [];
        req.uploadedFilenames[fieldName].push(filename);
        cb(null, filename);
    }
});

const storage1 = multer.diskStorage({
    destination: function (req, file, cb) {
        // Specify the destination folder for uploads
        cb(null, 'public/uploads/');
    },
    filename: function (req, file, cb) {
        // Generate unique filename and save it in the request object for later use
        const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
        req.uploadedFilenames1 = req.uploadedFilenames1 || [];
        req.uploadedFilenames1.push(uniqueSuffix + path.extname(file.originalname));
        cb(null, req.uploadedFilenames1[req.uploadedFilenames1.length - 1]);
    }
});

function generateRandomString(length) {
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let result = '';
    for (let i = 0; i < length; i++) {
        const randomIndex = Math.floor(Math.random() * characters.length);
        result += characters.charAt(randomIndex);
    }
    return result;
}

function generateUniqueId(name) {
    const randomPart = generateRandomString(6); // Generate a random string of length 6
    return `${name}-EB${randomPart}WX10`;
}

const checkMainCategoryExists = async (mcID) => {
    return new Promise((resolve, reject) => {
        pool.query('SELECT * FROM categories WHERE id = ?', [mcID], function (error, result) {
            if (error) {
                reject(error);
            } else {
                resolve(result.length > 0);
            }
        });
    });
};

const fetchFeatureData = async (featureIds) => {
    if (featureIds.length > 0) {
        let statement = `
        SELECT * 
        FROM features
        WHERE id IN (${featureIds.join(',')})
        `;
        return new Promise((resolve, reject) => {
            pool.query(statement, function (error, result) {
                if (error) {
                    reject(error);
                } else {
                    resolve(result);
                }
            });
        });
    }
    else {
        return new Promise((resolve, reject) => {
            resolve([]);
        });
    }
};

const checkSubCategory = async (scID) => {
    return new Promise((resolve, reject) => {
        pool.query('SELECT * FROM subcategory WHERE id = ?', [scID], function (error, result) {
            if (error) {
                reject(error);
            } else {
                resolve(result[0]);
            }
        });
    });
};

const checkSubCategoryExists = async (scID) => {
    return new Promise((resolve, reject) => {
        pool.query('SELECT * FROM subcategory WHERE id = ?', [scID], function (error, result) {
            if (error) {
                reject(error);
            } else {
                resolve(result.length > 0);
            }
        });
    });
};

const deleteUploadedFiles = async (filenames) => {
    if (!filenames || filenames.length === 0) return;
    for (const filename of filenames) {
        try {
            const filePath = path.join(__dirname, '..', '..', 'public', 'uploads', filename);
            await fs.promises.unlink(filePath);
            console.log(`Deleted file: ${filename}`);
        } catch (error) {
            console.error(`Error deleting file ${filename}: ${error.message}`);
        }
    }
};

// Set up multer upload configuration for multiple files
router.post('/', (req, res) => {
    const upload = multer({
        storage: storage
    }).fields([
        { name: 'images[]', maxCount: 5 }, // Handling multiple images
        { name: 'hover_img', maxCount: 1 }, // Handling a single hover image
        { name: 'main_img', maxCount: 1 } // Handling a single main image
    ]);

    upload(req, res, async function (err) {
        if (err instanceof multer.MulterError) {
            // A Multer error occurred when uploading.
            console.log(err);
            res.status(400).send('Error uploading files');
        } else if (err) {
            // An unknown error occurred when uploading.
            console.log(err);
            res.status(500).send('Error uploading files');
        } else {
            // Everything went fine.
            // Uploaded filenames based on keys can be accessed in req.uploadedFilenames object
            const imgUrls = {
                images: req.uploadedFilenames['images[]'] ? req.uploadedFilenames['images[]'].map(filename => 'uploads/' + filename) : [],
                hover_img: req.uploadedFilenames['hover_img'] ? 'uploads/' + req.uploadedFilenames['hover_img'][0] : null,
                main_img: req.uploadedFilenames['main_img'] ? 'uploads/' + req.uploadedFilenames['main_img'][0] : null
            };
            const { mcID, scID, heading, rating, descitptionDescription, vendor_id, discount, priceBasedOnType, tags, quantityTypes, pincodes, gst, isCODAvailable, dimentions, deliveryCharges, codCharges, sku,
                //  title, 
                description, price, brandID, highLights, available, quantity } = req.body;
            let { features, mfg } = req.body;

            // Validate required fields
            const missingFields = {};
            if (!mcID) missingFields.mcID = 'Main Category ID';
            if (!scID) missingFields.scID = 'Sub Category ID';
            if (!rating) missingFields.rating = 'Rating';
            if (!heading) missingFields.heading = 'Heading';
            if (!dimentions) missingFields.dimentions = 'Dimentions';
            if (!deliveryCharges) missingFields.deliveryCharges = 'deliveryCharges';
            if (!codCharges) missingFields.codCharges = 'codCharges';
	//            if (!priceBasedOnType) missingFields.priceBasedOnType = 'priceBasedOnType';
            if (!req.session?.user?.vendorID && !vendor_id) missingFields.vendor_id = 'Vendor Id';
            // if (!title) missingFields.title = 'Title';
            if (!description) missingFields.description = 'Description';
            if (!price) missingFields.price = 'Price';
            if (!brandID) missingFields.brandID = 'Brand ID';
            if (!quantity) missingFields.quantity = 'Quantity';
            if (!available) missingFields.available = 'Available';
            if (!gst) missingFields.gst = 'GST';
            if (!imgUrls['main_img']) missingFields.main_img = 'Main Image';
            if (!imgUrls['hover_img']) missingFields.hover_img = 'Hover Image';
            if (!features) {
                features = '[]';
            }
            else {
                features = JSON.stringify(features.split(','))
            }

            let variantSkus = []

            Object.keys(JSON.parse(quantityTypes)).map((key) => {
                JSON.parse(quantityTypes)[key].map((item) => {
                    variantSkus.push(generateUniqueId(item.type))
                })
            })

            // If any required fields are missing, return a 400 error with the missing fields
            if (Object.keys(missingFields).length > 0) {
                // Delete uploaded files if there are missing required fields
                const imagesFilenames = req.uploadedFilenames['images[]'] || [];
                const hoverImgFilename = req.uploadedFilenames['hover_img'] ? req.uploadedFilenames['hover_img'][0] : null;
                const mainImgFilename = req.uploadedFilenames['main_img'] ? req.uploadedFilenames['main_img'][0] : null;

                // Combine all filenames to delete
                const filenamesToDelete = [...imagesFilenames, hoverImgFilename, mainImgFilename].filter(filename => filename);

                // Delete the uploaded files
                await deleteUploadedFiles(filenamesToDelete);

                // Return response indicating missing required fields
                return res.status(400).json({ message: 'Missing required fields', missingFields });
            }

            const mainCategoryExists = await checkMainCategoryExists(mcID);
            if (!mainCategoryExists) {
                await deleteUploadedFiles(req.uploadedFilenames);
                return res.status(400).json({ message: 'Main Category does not exist' });
            }
            let title;

            if (!mfg) {
                mfg = ''
            }

            const subCategoryExists = await checkSubCategory(scID);
            if (!subCategoryExists) {
                await deleteUploadedFiles(req.uploadedFilenames);
                return res.status(400).json({ message: 'Sub Category does not exist' });
            }
            else {
                title = subCategoryExists.subCategoryName;
            }

            // let productAddStatement = 'INSERT INTO products (mcID, discount, price_based_on_type, main_img, description_desc, scID,heading, title, rating, description, price, brandID, highLights, productImages, available, status,quantity, sku, features, tags, quantityTypes, pincodes, gst,mfg, hover_img, variations_sku, is_cod_available, dimentions, delivery_charges, cod_charges, user_id';
            let productAddStatement = 'INSERT INTO products (mcID, discount, price_based_on_type, main_img, description_desc, scID,heading, title, rating, description, price, brandID, highLights, productImages, available, status,quantity, sku, features, tags, quantityTypes, pincodes, gst,mfg, hover_img, is_cod_available, dimentions, delivery_charges, cod_charges, user_id';
            // let valuesObject = '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?, ?, ?, ?, ?';
            let valuesObject = '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?, ?, ?, ?';
            let values = [mcID, discount === '' ? 0 : discount, priceBasedOnType, imgUrls['main_img'], descitptionDescription, scID, heading, title, rating, description, price, brandID, highLights, JSON.stringify(imgUrls['images']), available, 1, quantity, sku, features, tags, quantityTypes, pincodes, gst, mfg, imgUrls['hover_img'], isCODAvailable, dimentions, deliveryCharges, codCharges];
            // let values = [mcID, discount, priceBasedOnType, JSON.stringify(imgUrls['main_img']), descitptionDescription, scID, heading, title, rating, description, price, brandID, highLights, JSON.stringify(imgUrls['images']), available, 1, quantity, sku, features, tags, quantityTypes, pincodes, gst, mfg, imgUrls['hover_img'], JSON.stringify(variantSkus), isCODAvailable, dimentions, deliveryCharges, codCharges];
            if (req.session?.user?.vendorID) {
                values.push(req.session?.user?.ID);
            }
            else {
                values.push(vendor_id);
            }
            productAddStatement = productAddStatement + ') VALUES ' + valuesObject + ')';

            // Insert the new product into the database
            await pool.query(productAddStatement,
                values, function (error, result) {
                    if (error) {
                        console.error(error.message);
                        return res.status(500).json({ message: 'Internal server error' });
                    } else {
                        // Assuming your table has an auto-increment primary key called 'id'
                        const insertedId = result.insertId;

                        // Fetch the inserted data using the insertedId
                        pool.query('SELECT * FROM products WHERE id = ?', [insertedId], function (selectError, selectResult) {
                            if (selectError) {
                                console.error(selectError.message);
                                return res.status(500).json({ message: 'Error fetching inserted data' });
                            } else {
                                // Return the newly inserted product
                                return res.json(selectResult[0]);
                            }
                        });
                    }
                });
        }
    });
});

router.get('/LuxuryMeetsSustainability/:type', async (req, res) => {
    try {
        let { type } = req.params;
        await pool.query('SELECT productsnew.*, brands.ID as brandid,brands.brandName as brandName,vendors.name as vendorName, (price+(price * (tax / 100))) AS price_with_gst FROM productsnew JOIN vendors ON vendors.vendorID = productsnew.vendor_id JOIN brands ON brands.ID = productsnew.brand_id where productsnew.luxury_meets_sustainability = ? AND productsnew.status = 1 AND productsnew.is_deleted = 0', [type], async function (error, result, fields) {
            if (error) {
                console.error(error.message);
                return res.status(500).json({ message: 'Internal server error' });
            }
            for (let product of result) {
                if (product.features) {
                    let featureIds = JSON.parse(product.features);
                    let features = await fetchFeatureData(featureIds);
                    product.featuresData = features;
                }
            }
            res.json(result);
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});

router.get('/filters/:type/:mcID?/:scID?', async (req, res) => {
    try {
        let { type, mcID, scID } = req.params;
        let statement;
        let values;
        let selectedType;
        switch (type) {
            case 'hotproducts':
                selectedType = 'hottest_products'
                break;
            case 'ecofrnddeals':
                selectedType = 'echo_friendly_deals'
                break;
            case 'deals_of_the_day':
                selectedType = 'deals_of_the_day'
                break;
            case 'clothing':
                selectedType = 'clothing'
                break;
            case 'sustanable_and_ecofrndly':
                selectedType = 'sus_and_eco_friendly'
                break;
            case 'recentlyadded':
                selectedType = 'recently_added'
                break;
            case 'toppicks':
                selectedType = 'top_picks'
                break;
            case 'banners':
                selectedType = 'is_banner'
                break;
        }
        if (mcID && scID) {
            statement = `SELECT *, (price+(price * (gst / 100))) AS price_with_gst FROM products where ${selectedType} = ? AND is_deleted = 0 AND mcID = ? AND scID = ?`;
            values = [type, mcID, scID]
        }
        else if (mcID) {
            statement = `SELECT *, (price+(price * (gst / 100))) AS price_with_gst FROM products where ${selectedType} = ? AND is_deleted = 0 AND mcID= ?`;
            values = [type, mcID]
        }
        else if (type) {
            statement = `SELECT *, (price+(price * (gst / 100))) AS price_with_gst FROM products where ${selectedType} = ? AND is_deleted = 0`;
            values = [type]
        }
        await pool.query(statement, values, async function (error, result, fields) {
            if (error) {
                return res.status(500).json({ message: 'Internal server error' });
            }
            for (let product of result) {
                if (product.features) {
                    let featureIds = JSON.parse(product.features);
                    let features = await fetchFeatureData(featureIds);
                    product.featuresData = features;
                }
            }
            res.json(result);
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});

router.post('/updatehotproducts', async (req, res) => {
    const selectedProductIds = req.body; // Array of selected product IDs

    try {
        const updateSelectedProductsSQL = `UPDATE products SET hottest_products = '1' WHERE id IN (?) `;
        await pool.query(updateSelectedProductsSQL, [selectedProductIds]);

        // Update other products in MySQL
        const updateOtherProductsSQL = `UPDATE products SET hottest_products = '0' WHERE id NOT IN (?)`;
        await pool.query(updateOtherProductsSQL, [selectedProductIds]);

        res.json({ success: true, message: `Products updated successfully.` });
    } catch (error) {
        console.error('Error updating products:', error);
        res.status(500).json({ success: false, message: 'Failed to update products.' });
    }
});

router.get('/SustainableClothing/:type', async (req, res) => {
    try {
        let { type } = req.params;
        await pool.query('SELECT productsnew.*, brands.ID as brandid,brands.brandName as brandName,vendors.name as vendorName, (price+(price * (tax / 100))) AS price_with_gst FROM productsnew JOIN vendors ON vendors.vendorID = productsnew.vendor_id JOIN brands ON brands.ID = productsnew.brand_id where productsnew.sustainable_clothing = ? AND productsnew.status = 1 AND productsnew.is_deleted = 0', [type], async function (error, result, fields) {
            if (error) {
                console.error(error.message);
                return res.status(500).json({ message: 'Internal server error' });
            }
            for (let product of result) {
                if (product.features) {
                    let featureIds = JSON.parse(product.features);
                    let features = await fetchFeatureData(featureIds);
                    product.featuresData = features;
                }
            }
            res.json(result);
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});

router.post('/updateecofrnddeals', async (req, res) => {
    const selectedProductIds = req.body; // Array of selected product IDs

    try {
        const updateSelectedProductsSQL = `UPDATE products SET echo_friendly_deals = '1' WHERE id IN (?)`;
        await pool.query(updateSelectedProductsSQL, [selectedProductIds]);

        // Update other products in MySQL
        const updateOtherProductsSQL = `UPDATE products SET echo_friendly_deals = '0' WHERE id NOT IN (?)`;
        await pool.query(updateOtherProductsSQL, [selectedProductIds]);

        res.json({ success: true, message: `Products updated successfully.` });
    } catch (error) {
        console.error('Error updating products:', error);
        res.status(500).json({ success: false, message: 'Failed to update products.' });
    }
});


router.get('/EcoFriendlygifting/:type', async (req, res) => {
    try {
        let { type } = req.params;
        await pool.query('SELECT productsnew.*, brands.ID as brandid,brands.brandName as brandName,vendors.name as vendorName, (price+(price * (tax / 100))) AS price_with_gst FROM productsnew JOIN vendors ON vendors.vendorID = productsnew.vendor_id JOIN brands ON brands.ID = productsnew.brand_id where productsnew.eco_friendly_gifting = ? AND productsnew.status = 1 AND productsnew.is_deleted = 0', [type], async function (error, result, fields) {
            if (error) {
                console.error(error.message);
                return res.status(500).json({ message: 'Internal server error' });
            }
            for (let product of result) {
                if (product.features) {
                    let featureIds = JSON.parse(product.features);
                    let features = await fetchFeatureData(featureIds);
                    product.featuresData = features;
                }
            }
            res.json(result);
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});

router.post('/updatedeals_of_the_day', async (req, res) => {
    const selectedProductIds = req.body; // Array of selected product IDs

    // Update the products in MySQL
    try {
        const updateSelectedProductsSQL = `UPDATE products SET deals_of_the_day = '1' WHERE id IN (?)`;
        await pool.query(updateSelectedProductsSQL, [selectedProductIds]);

        // Update other products in MySQL
        const updateOtherProductsSQL = `UPDATE products SET deals_of_the_day = '0' WHERE id NOT IN (?)`;
        await pool.query(updateOtherProductsSQL, [selectedProductIds]);

        res.json({ success: true, message: `Products updated successfully.` });
    } catch (error) {
        console.error('Error updating products:', error);
        res.status(500).json({ success: false, message: 'Failed to update products.' });
    }
});

router.get('/HealthySnacks/:type', async (req, res) => {
    try {
        let { type } = req.params;
        await pool.query('SELECT productsnew.*, brands.ID as brandid,brands.brandName as brandName,vendors.name as vendorName, (price+(price * (tax / 100))) AS price_with_gst FROM productsnew JOIN vendors ON vendors.vendorID = productsnew.vendor_id JOIN brands ON brands.ID = productsnew.brand_id where productsnew.healthy_snacks = ? AND productsnew.status = 1 AND productsnew.is_deleted = 0', [type], async function (error, result, fields) {
            if (error) {
                console.error(error.message);
                return res.status(500).json({ message: 'Internal server error' });
            }
            for (let product of result) {
                if (product.features) {
                    let featureIds = JSON.parse(product.features);
                    let features = await fetchFeatureData(featureIds);
                    product.featuresData = features;
                }
            }
            res.json(result);
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});

router.get('/HomeEssentials/:type', async (req, res) => {
    try {
        let { type } = req.params;
        await pool.query('SELECT productsnew.*, brands.ID as brandid,brands.brandName as brandName,vendors.name as vendorName, (price+(price * (tax / 100))) AS price_with_gst FROM productsnew JOIN vendors ON vendors.vendorID = productsnew.vendor_id JOIN brands ON brands.ID = productsnew.brand_id where productsnew.home_essentials = ? AND productsnew.status = 1 AND productsnew.is_deleted = 0', [type], async function (error, result, fields) {
            if (error) {
                console.error(error.message);
                return res.status(500).json({ message: 'Internal server error' });
            }
            for (let product of result) {
                if (product.features) {
                    let featureIds = JSON.parse(product.features);
                    let features = await fetchFeatureData(featureIds);
                    product.featuresData = features;
                }
            }
            res.json(result);
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});

router.get('/Bestsellers/:type', async (req, res) => {
    try {
        let { type } = req.params;
        await pool.query('SELECT productsnew.*, brands.ID as brandid,brands.brandName as brandName,vendors.name as vendorName, (price+(price * (tax / 100))) AS price_with_gst FROM productsnew JOIN vendors ON vendors.vendorID = productsnew.vendor_id JOIN brands ON brands.ID = productsnew.brand_id where productsnew.bestsellers = ? AND productsnew.status = 1 AND productsnew.is_deleted = 0', [type], async function (error, result, fields) {
            if (error) {
                console.error(error.message);
                return res.status(500).json({ message: 'Internal server error' });
            }
            for (let product of result) {
                if (product.features) {
                    let featureIds = JSON.parse(product.features);
                    let features = await fetchFeatureData(featureIds);
                    product.featuresData = features;
                }
            }
            res.json(result);
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});

router.post('/updateclothing', async (req, res) => {
    const selectedProductIds = req.body; // Array of selected product IDs

    try {
        const updateSelectedProductsSQL = `UPDATE products SET clothing = '1' WHERE id IN (?)`;
        await pool.query(updateSelectedProductsSQL, [selectedProductIds]);

        // Update other products in MySQL
        const updateOtherProductsSQL = `UPDATE products SET clothing = '0' WHERE id NOT IN (?)`;
        await pool.query(updateOtherProductsSQL, [selectedProductIds]);

        res.json({ success: true, message: `Products updated successfully.` });
    } catch (error) {
        console.error('Error updating products:', error);
        res.status(500).json({ success: false, message: 'Failed to update products.' });
    }
});

router.get('/sustanable_and_ecofrndly/:type', async (req, res) => {
    try {
        let { type } = req.params;
        await pool.query('SELECT *, (price+(price * (tax / 100))) AS price_with_gst FROM productsnew where sus_and_eco_friendly = ? AND status = 1 AND is_deleted = 0', [type], async function (error, result, fields) {
            if (error) {
                console.error(error.message);
                return res.status(500).json({ message: 'Internal server error' });
            }
            if (result && Symbol.iterator in Object(result)) {
                // Proceed with iteration
                for (let product of result) {
                    if (product.features) {
                        let featureIds = JSON.parse(product.features);
                        let features = await fetchFeatureData(featureIds);
                        product.featuresData = features;
                    }
                }
            } else {
                // Handle non-iterable case
                console.error("result is not iterable");
            }
           
            res.json(result);
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});

router.post('/updatesustanable_and_ecofrndly', async (req, res) => {
    const selectedProductIds = req.body; // Array of selected product IDs

    try {
        const updateSelectedProductsSQL = `UPDATE products SET sus_and_eco_friendly = '1' WHERE id IN (?)`;
        await pool.query(updateSelectedProductsSQL, [selectedProductIds]);

        // Update other products in MySQL
        const updateOtherProductsSQL = `UPDATE products SET sus_and_eco_friendly = '0' WHERE id NOT IN (?)`;
        await pool.query(updateOtherProductsSQL, [selectedProductIds]);

        res.json({ success: true, message: `Products updated successfully.` });
    } catch (error) {
        console.error('Error updating products:', error);
        res.status(500).json({ success: false, message: 'Failed to update products.' });
    }

});

// Get all products items
router.get('/recentlyadded/:type', async (req, res) => {
    try {
        let { type } = req.params;
        let statement = 'SELECT productsnew.*, brands.ID as brandid,brands.brandName as brandName,vendors.name as vendorName, (price+(price * (tax / 100))) AS price_with_gst FROM productsnew JOIN vendors ON vendors.vendorID = productsnew.vendor_id JOIN brands ON brands.ID = productsnew.brand_id where productsnew.status = 1 AND productsnew.is_deleted = 0 ORDER BY ID DESC LIMIT 10';
        await pool.query(statement, async function (error, result, fields) {
            if (error) {
                console.error(error.message);
                res.status(500).json({ message: 'Internal server error' });
            };
            for (let product of result) {
                if (product.features) {
                    let featureIds = JSON.parse(product.features);
                    let features = await fetchFeatureData(featureIds);
                    product.featuresData = features;
                }
                //console.log(product)
            }
            res.json(result);
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});

router.post('/updaterecentlyadded', async (req, res) => {
    const selectedProductIds = req.body;

    try {
        const updateSelectedProductsSQL = `UPDATE products SET recently_added = '1' WHERE id IN (?)`;
        await pool.query(updateSelectedProductsSQL, [selectedProductIds]);

        // Update other products in MySQL
        const updateOtherProductsSQL = `UPDATE products SET recently_added = '0' WHERE id NOT IN (?)`;
        await pool.query(updateOtherProductsSQL, [selectedProductIds]);

        res.json({ success: true, message: `Products updated successfully.` });
    } catch (error) {
        console.error('Error updating products:', error);
        res.status(500).json({ success: false, message: 'Failed to update products.' });
    }

});

// get all top products
router.get('/toppicks/:type', async (req, res) => {
    try {
        let { type } = req.params;
        // const statement = "SELECT *, (price+(price * (tax / 100))) AS price_with_gst FROM productsnew where top_picks = ? AND status = 1 AND is_deleted = 0";
        const statement = "SELECT productsnew.*, brands.ID as brandid,brands.brandName as brandName,vendors.name as vendorName, (price+(price * (tax / 100))) AS price_with_gst FROM productsnew JOIN vendors ON vendors.vendorID = productsnew.vendor_id JOIN brands ON brands.ID = productsnew.brand_id where productsnew.top_picks = ? AND productsnew.status = 1 AND productsnew.is_deleted = 0"
        await pool.query(statement, [type], async function (error, result, fields) {
            if (error) {
                console.error(error.message);
                res.status(500).json({ message: 'Internal server error' });
            };
            for (let product of result) {
                if (product.features) {
                    let featureIds = JSON.parse(product.features);
                    let features = await fetchFeatureData(featureIds);
                    product.featuresData = features;
                }
            }
            res.json(result);
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
})

router.post('/updatetoppicks', async (req, res) => {
    const selectedProductIds = req.body;

    try {
        const updateSelectedProductsSQL = `UPDATE products SET top_picks = '1' WHERE id IN (?)`;
        await pool.query(updateSelectedProductsSQL, [selectedProductIds]);

        // Update other products in MySQL
        const updateOtherProductsSQL = `UPDATE products SET top_picks = '0' WHERE id NOT IN (?)`;
        await pool.query(updateOtherProductsSQL, [selectedProductIds]);

        res.json({ success: true, message: `Products updated successfully.` });
    } catch (error) {
        console.error('Error updating products:', error);
        res.status(500).json({ success: false, message: 'Failed to update products.' });
    }

});

// get all top products
router.get('/banners/:type', async (req, res) => {
    try {
        let { type } = req.params;
        const statement = "SELECT *, (price+(price * (gst / 100))) AS price_with_gst FROM products where is_banner = ? AND is_deleted = 0";
        await pool.query(statement, [type], async function (error, result, fields) {
            if (error) {
                console.error(error.message);
                res.status(500).json({ message: 'Internal server error' });
            };
            for (let product of result) {
                if (product.features) {
                    let featureIds = JSON.parse(product.features);
                    let features = await fetchFeatureData(featureIds);
                    product.featuresData = features;
                }
            }
            res.json(result);
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
})

// Get all cart items
router.post('/requiredProducts', async (req, res) => {
    try {
        let { productIds } = req.body;
        if (!productIds) res.status(500).json({ message: 'productIds is mandatory' });
        await pool.query('SELECT *, 1 as userQuantity FROM productsnew WHERE ID IN (?) AND status = 1', [productIds], function (error, result, fields) {
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

// Get all cart items
router.get('/brands/:id', async (req, res) => {
    try {
        let { id } = req.params;
        //console.log(id)
        await pool.query('SELECT * FROM productsnew WHERE brand_id = ? AND status = 1', [id], function (error, result, fields) {
            if (error) {
                console.error(error.message);
                return res.status(500).json({ message: 'Internal server error' });
            }
            //console.log(result)
            res.json(result);
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});


router.post('/updatebanners', async (req, res) => {
    const selectedProductIds = req.body;

    try {
        const updateSelectedProductsSQL = `UPDATE products SET is_banner = '1' WHERE id IN (?)`;
        await pool.query(updateSelectedProductsSQL, [selectedProductIds]);

        // Update other products in MySQL
        const updateOtherProductsSQL = `UPDATE products SET is_banner = '0' WHERE id NOT IN (?)`;
        await pool.query(updateOtherProductsSQL, [selectedProductIds]);

        res.json({ success: true, message: `Products updated successfully.` });
    } catch (error) {
        console.error('Error updating products:', error);
        res.status(500).json({ success: false, message: 'Failed to update products.' });
    }

});

// Get all products items
router.get('/', async (req, res) => {
    try {
        let statement = 'SELECT *, (price+(price * (gst / 100))) AS price_with_gst FROM products WHERE is_deleted = 0 AND status = 1';
        if (req.session?.user?.vendorID) {
            statement = statement + ' WHERE user_id = ' + req.session?.user?.ID
        }
        statement = statement + ' AND  quantity > 0'
        await pool.query(statement, async function (error, result, fields) {
            if (error) {
                console.error(error.message);
                res.status(500).json({ message: 'Internal server error' });
            };
            for (let product of result) {
                if (product.features) {
                    let featureIds = JSON.parse(product.features);
                    let features = await fetchFeatureData(featureIds);
                    product.featuresData = features;
                }
            }
            res.json(result);
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});

// Get all products items
router.get('/getall', async (req, res) => {
    try {
        let statement = 'SELECT *, (price+(price * (gst / 100))) AS price_with_gst FROM products WHERE is_deleted = 0';
        if (req.session?.user?.vendorID) {
            statement = statement + ' WHERE user_id = ' + req.session?.user?.ID
        }
        await pool.query(statement, async function (error, result, fields) {
            if (error) {
                console.error(error.message);
                res.status(500).json({ message: 'Internal server error' });
            };
            for (let product of result) {
                if (product.features) {
                    let featureIds = JSON.parse(product.features);
                    let features = await fetchFeatureData(featureIds);
                    product.featuresData = features;
                }
            }
            res.json(result);
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});

// Get a products by ID
router.get('/:id', async (req, res) => {
    try {
        const { id } = req.params;
        //console.log(id)
        await pool.query('SELECT productsnew.*, brands.*, (price+(price * (tax / 100))) AS price_with_gst FROM productsnew JOIN brands ON brands.ID = productsnew.brand_id WHERE productsnew.ID = ? AND productsnew.status = 1', [id], async function (error, data) {
            if (error) {
                console.error(error.message);
                res.status(500).json({ message: 'Internal server error' });
            };
            //console.log(data)
            if (data.length > 0) {
                if (data[0].features) {
                    let featureIds = JSON.parse(data[0].features);
                    let features = await fetchFeatureData(featureIds);
                    data[0].featuresData = features;
                }
            }
            res.json(data[0]);
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});

// // // Get products by mcID
// router.get('/mcID/:id', async (req, res) => {
//     try {
//         const { id } = req.params;
//         const categoryIds = id.split(','); // Split the parameter string into an array of category IDs

//         let statement = `
//         SELECT productsnew.*, 
//                brands.ID as brandid,
//                brands.brandName as brandName,
//                vendors.name as vendorName,
//                categories.categoryName,
//                subcategory.subCategoryName, 
//                (price+(price * (tax / 100))) AS price_with_gst 
//         FROM productsnew 
//         JOIN brands ON brands.ID = productsnew.brand_id 
//         JOIN vendors ON vendors.vendorID = productsnew.vendor_id 
//         JOIN categories ON categories.ID = productsnew.category 
//         JOIN subcategory ON subcategory.ID = productsnew.subcategory
//         WHERE FIND_IN_SET(?, productsnew.category)
//         AND productsnew.is_deleted = 0
//         AND productsnew.status = 1
//     `;

//         if (req.session?.user?.vendorID) {
//             statement += ' AND productsnew.user_id = ' + req.session?.user?.ID;
//         }
        
//         await pool.query(statement, [categoryIds], async function (error, data) {
//             if (error) {
//                 console.error(error.message);
//                 return res.status(500).json({ message: 'Internal server error' });
//             }
//             if (data && Symbol.iterator in Object(data)) {
//                 // Proceed with iteration
//                 for (let product of data) {
//                     if (product.features) {
//                         let featureIds = JSON.parse(product.features);
//                         let features = await fetchFeatureData(featureIds);
//                         product.featuresData = features;
//                     }
//                 }
//             } else {
//                 // Handle non-iterable case
//                 console.error("result is not iterable");
//             }
            
//             res.json(data);
//         });
//     } catch (error) {
//         console.error(error.message);
//         res.status(500).json({ message: 'Internal server error' });
//     }
// });



// Get products by mcID with pagination, sorting, and total count
router.get('/mcID/:id', async (req, res) => {
    try {
        const { id } = req.params;
        const page = parseInt(req.query.params.page) || 1; // Current page number, default to 1 if not provided
        const perPage = parseInt(req.query.params.per_page) || 10; // Number of products per page, default to 10 if not provided
        const offset = (page - 1) * perPage; // Calculate offset based on page number
        
        // Query to fetch products with pagination and sorting
        let productsQuery = `
            SELECT productsnew.*, 
                   brands.ID as brandid,
                   brands.brandName as brandName,
                   vendors.name as vendorName,
                   categories.categoryName,
                   subcategory.subCategoryName, 
                   (price + (price * (tax / 100))) AS price_with_gst 
            FROM productsnew 
            JOIN vendors ON vendors.vendorID = productsnew.vendor_id 
            JOIN brands ON brands.ID = productsnew.brand_id 
            JOIN categories ON categories.ID = productsnew.category 
            JOIN subcategory ON subcategory.ID = productsnew.subcategory
            WHERE FIND_IN_SET(?, productsnew.category)
            AND productsnew.is_deleted = 0
            AND productsnew.status = 1
            ORDER BY productsnew.title
            LIMIT ?, ?
        `;

        // Query to fetch total count of products without pagination
        let countQuery = `
            SELECT COUNT(*) AS total_count
            FROM productsnew 
            JOIN vendors ON vendors.vendorID = productsnew.vendor_id 
            JOIN brands ON brands.ID = productsnew.brand_id 
            JOIN categories ON categories.ID = productsnew.category 
            JOIN subcategory ON subcategory.ID = productsnew.subcategory
            WHERE FIND_IN_SET(?, productsnew.category)
            AND productsnew.is_deleted = 0
            AND productsnew.status = 1
        `;

        if (req.session?.user?.vendorID) {
            productsQuery += ' AND productsnew.user_id = ' + req.session?.user?.ID;
            countQuery += ' AND productsnew.user_id = ' + req.session?.user?.ID;
        }

        // Execute both queries concurrently using Promise.all
        const [productsData, countData] = await Promise.all([
            new Promise((resolve, reject) => {
                pool.query(productsQuery, [id, offset, perPage], (error, data) => {
                    if (error) {
                        console.error(error.message);
                        reject(error);
                    } else {
                        resolve(data);
                    }
                });
            }),
            new Promise((resolve, reject) => {
                pool.query(countQuery, [id], (error, data) => {
                    if (error) {
                        console.error(error.message);
                        reject(error);
                    } else {
                        resolve(data[0].total_count); // Extract total count from the result
                    }
                });
            })
        ]);

        // Attach featuresData to each product
        for (let product of productsData) {
            if (product.features) {
                let featureIds = JSON.parse(product.features);
                let features = await fetchFeatureData(featureIds);
                product.featuresData = features;
            }
        }

        // Construct response object with pagination and total count
        const response = {
            total_count: countData,
            products: productsData
        };

        res.json(response);
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});


// Get products by mcID
router.get('/mcID/getall/:id', async (req, res) => {
    try {
        const { id } = req.params;
        let statement = 'SELECT *, (price+(price * (tax / 100))) AS price_with_gst FROM productsnew WHERE WHERE FIND_IN_SET(?, category) AND status = 1 AND is_deleted = 0';
        if (req.session?.user?.vendorID) {
            statement = statement + ' AND user_id = ' + req.session?.user?.ID
        }
        await pool.query(statement, [id], async function (error, data) {
            if (error) {
                console.error(error.message);
                res.status(500).json({ message: 'Internal server error' });
            };
            for (let product of data) {
                if (product.features) {
                    let featureIds = JSON.parse(product.features);
                    let features = await fetchFeatureData(featureIds);
                    product.featuresData = features;
                }
            }
            res.json(data);
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});

// // Get products by scID
// router.get('/scID/:id', async (req, res) => {
//     try {
//         const { id } = req.params;
//         // let statement = 'SELECT *, (price+(price * (tax / 100))) AS price_with_gst FROM productsnew WHERE subcategory = ?  AND is_deleted = 0 AND status = 1';

//         // let statement = 'SELECT productsnew.*, brands.ID as brandid,brands.brandName as brandName,categories.categoryName,subcategory.subCategoryName, (price+(price * (tax / 100))) AS price_with_gst FROM productsnew JOIN brands ON brands.ID = productsnew.brand_id JOIN categories ON categories.ID = productsnew.category JOIN subcategory ON subcategory.ID = productsnew.subcategory WHERE productsnew.subcategory = ?  AND productsnew.is_deleted = 0 AND productsnew.status = 1'

//         let statement = 'SELECT productsnew.*, brands.ID as brandid,brands.brandName as brandName,vendors.name as vendorName,categories.categoryName,subcategory.subCategoryName, (price+(price * (tax / 100))) AS price_with_gst FROM productsnew JOIN vendors ON vendors.vendorID = productsnew.vendor_id JOIN brands ON brands.ID = productsnew.brand_id JOIN categories ON categories.ID = productsnew.category JOIN subcategory ON subcategory.ID = productsnew.subcategory WHERE FIND_IN_SET(?, productsnew.subcategory)  AND productsnew.is_deleted = 0 AND productsnew.status = 1'

//         //WHERE FIND_IN_SET(?, productsnew.category)
//         //console.log(req.session?.user?.vendorID)
//         if (req.session?.user?.vendorID) {
//             statement = statement + ' AND user_id = ' + req.session?.user?.ID
//         }
//         await pool.query(statement, [id], async function (error, data) {
//             if (error) {
//                 console.error(error.message);
//                 res.status(500).json({ message: 'Internal server error' });
//             };
//             for (let product of data) {
//                 if (product.features) {
//                     let featureIds = JSON.parse(product.features);
//                     let features = await fetchFeatureData(featureIds);
//                     product.featuresData = features;
//                 }
//             }
//             res.json(data);
//         });
//     } catch (error) {
//         console.error(error.message);
//         res.status(500).json({ message: 'Internal server error' });
//     }
// });


// Get products by scID with pagination
// Get products by scID with pagination, sorting, and total count
router.get('/scID/:id', async (req, res) => {
    try {
        const { id } = req.params;
        const page = parseInt(req.query.params.page) || 1; // Current page number, default to 1 if not provided
        const perPage = parseInt(req.query.params.per_page) || 10; // Number of products per page, default to 10 if not provided
        const offset = (page - 1) * perPage; 

        // Query to fetch products with pagination and sorting
        let productsQuery = `
            SELECT productsnew.*, 
                   brands.ID as brandid,
                   brands.brandName as brandName,
                   vendors.name as vendorName,
                   categories.categoryName,
                   subcategory.subCategoryName, 
                   (price + (price * (tax / 100))) AS price_with_gst 
            FROM productsnew 
            JOIN vendors ON vendors.vendorID = productsnew.vendor_id 
            JOIN brands ON brands.ID = productsnew.brand_id 
            JOIN categories ON categories.ID = productsnew.category 
            JOIN subcategory ON subcategory.ID = productsnew.subcategory
            WHERE FIND_IN_SET(?, productsnew.subcategory)
            AND productsnew.is_deleted = 0
            AND productsnew.status = 1
            ORDER BY productsnew.title
            LIMIT ?, ?
        `;

        // Query to fetch total count of products without pagination
        let countQuery = `
            SELECT COUNT(*) AS total_count
            FROM productsnew 
            JOIN vendors ON vendors.vendorID = productsnew.vendor_id 
            JOIN brands ON brands.ID = productsnew.brand_id 
            JOIN categories ON categories.ID = productsnew.category 
            JOIN subcategory ON subcategory.ID = productsnew.subcategory
            WHERE FIND_IN_SET(?, productsnew.subcategory)
            AND productsnew.is_deleted = 0
            AND productsnew.status = 1
        `;

        if (req.session?.user?.vendorID) {
            productsQuery += ' AND productsnew.user_id = ' + req.session?.user?.ID;
            countQuery += ' AND productsnew.user_id = ' + req.session?.user?.ID;
        }

        // Execute both queries concurrently using Promise.all
        const [productsData, countData] = await Promise.all([
            new Promise((resolve, reject) => {
                pool.query(productsQuery, [id, offset, perPage], (error, data) => {
                    if (error) {
                        console.error(error.message);
                        reject(error);
                    } else {
                        resolve(data);
                    }
                });
            }),
            new Promise((resolve, reject) => {
                pool.query(countQuery, [id], (error, data) => {
                    if (error) {
                        console.error(error.message);
                        reject(error);
                    } else {
                        resolve(data[0].total_count); // Extract total count from the result
                    }
                });
            })
        ]);

        // Attach featuresData to each product
        for (let product of productsData) {
            if (product.features) {
                let featureIds = JSON.parse(product.features);
                let features = await fetchFeatureData(featureIds);
                product.featuresData = features;
            }
        }

        // Construct response object with pagination and total count
        const response = {
            total_count: countData,
            products: productsData
        };

        res.json(response);
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});






// Get products by scID
router.get('/scID/getall/:id', async (req, res) => {
    try {
        const { id } = req.params;
        let statement = 'SELECT *, (price+(price * (gst / 100))) AS price_with_gst FROM products WHERE scID = ?  AND is_deleted = 0';
        //console.log(req.session?.user)
        if (req.session?.user?.vendorID) {
            statement = statement + ' AND user_id = ' + req.session?.user?.ID
        }
        await pool.query(statement, [id], async function (error, data) {
            if (error) {
                console.error(error.message);
                res.status(500).json({ message: 'Internal server error' });
            };
            for (let product of data) {
                //console.log(product.features)
                if (product.features) {
                    let featureIds = JSON.parse(product.features);
                    let features = await fetchFeatureData(featureIds);
                    product.featuresData = features;
                }
            }
            res.json(data);
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});

// Update a product
router.put('/:id', (req, res) => {
    // Upload multiple files
    const upload = multer({
        storage: storage
    }).fields([
        { name: 'images[]', maxCount: 5 }, // Handling multiple images
        { name: 'hover_img', maxCount: 1 }, // Handling a single hover image
        { name: 'main_img', maxCount: 1 } // Handling a single main image
    ]);

    upload(req, res, async function (err) {
        if (err instanceof multer.MulterError) {
            // A Multer error occurred when uploading.
            console.log(err);
            res.status(400).send('Error uploading files');
        } else if (err) {
            // An unknown error occurred when uploading.
            console.log(err);
            res.status(500).send('Error uploading files');
        } else {
            // Everything went fine.
            // Uploaded filenames based on keys can be accessed in req.uploadedFilenames object
            const imgUrls = req.uploadedFilenames ? {
                images: req.uploadedFilenames['images[]'] ? req.uploadedFilenames['images[]'].map(filename => 'uploads/' + filename) : [],
                hover_img: req.uploadedFilenames['hover_img'] ? 'uploads/' + req.uploadedFilenames['hover_img'][0] : null,
                main_img: req.uploadedFilenames['main_img'] ? 'uploads/' + req.uploadedFilenames['main_img'][0] : null
            } : { images: [], hover_img: '', main_img: '' };

            try {

                const { mcID, scID, heading, rating, descitptionDescription, discount, priceBasedOnType, tags, quantityTypes, vendor_id, pincodes, gst, isCODAvailable, dimentions, deliveryCharges, codCharges, sku,
                    // title,
                    description, price, brandID, highLights, available, status, quantity } = req.body;
                let { features, mfg } = req.body;
                const { id } = req.params;

                // Validate required fields
                const missingFields = {};
                if (!mcID) missingFields.mcID = 'Main Category ID';
                if (!features) features = '[]';
                else {
                    features = JSON.stringify(features.split(','))
                }
                if (!scID) missingFields.scID = 'Sub Category ID';
                if (!rating) missingFields.rating = 'rating';
                if (!dimentions) missingFields.dimentions = 'dimentions';
                if (!heading) missingFields.heading = 'Heading';
                if (!discount) missingFields.discount = 'discount'
                if (!gst) missingFields.gst = 'GST';
                if (!req.session?.user?.vendorID && !vendor_id) missingFields.vendor_id = 'Vendor Id';
                if (!deliveryCharges) missingFields.deliveryCharges = 'deliveryCharges';
//                if (!priceBasedOnType) missingFields.priceBasedOnType = 'priceBasedOnType'
                // if (!title) missingFields.title = 'Title';
                if (!description) missingFields.description = 'Description';
                // if (!descitptionDescription) missingFields.descitptionDescription = 'Descitption Description';
                if (!price) missingFields.price = 'Price';
                if (!brandID) missingFields.brandID = 'Brand ID';
                if (!quantity) missingFields.quantity = 'Quantity';
                if (!codCharges) missingFields.codCharges = 'codCharges';
                if (!available) missingFields.available = 'Available';
                if (!status) missingFields.status = 'Status';

                if (Object.keys(missingFields).length > 0) {
                    await deleteUploadedFiles(req.uploadedFilenames);
                    return res.status(400).json({ message: 'Missing required fields', missingFields });
                }

                let variantSkus = []

                Object.keys(JSON.parse(quantityTypes)).map((item1) => {
                    JSON.parse(quantityTypes)[item1].map((item) => {
                        variantSkus.push(generateUniqueId(item.type))
                    })
                })

                const mainCategoryExists = await checkMainCategoryExists(mcID);
                if (!mainCategoryExists) {
                    await deleteUploadedFiles(req.uploadedFilenames);
                    return res.status(400).json({ message: 'Main Category does not exist' });
                }

                if (!mfg) {
                    mfg = ''
                }

                const subCategoryExists = await checkSubCategoryExists(scID);
                if (!subCategoryExists) {
                    await deleteUploadedFiles(req.uploadedFilenames);
                    return res.status(400).json({ message: 'Sub Category does not exist' });
                }

                // Check if new images were uploaded
                if (imgUrls['images'].length > 0 || imgUrls['hover_img'] || imgUrls['main_img']) {
                    // Delete old files associated with the product
                    pool.query('SELECT productImages FROM products WHERE id = ?', [id], async function (error, oldProduct) {
                        if (error) {
                            console.error(error.message);
                            return res.status(500).json({ message: 'Internal server error' });
                        }

                        if (!oldProduct || oldProduct.length === 0) {
                            return res.status(404).json({ message: 'Product not found' });
                        }

                        const oldImageUrls = JSON.parse(oldProduct[0].productImages);
                        const oldDescriptionImageUrls = oldProduct[0].main_img;
                        if (imgUrls['images'].length > 0) {
                            for (const imageUrl of oldImageUrls) {
                                try {
                                    // Delete the file from the uploads folder
                                    const filePath = path.join(__dirname, '..', '..', 'public', imageUrl);
                                    await fs.promises.unlink(filePath);
                                } catch (error) {
                                    console.error('Error deleting file:', error.message);
                                }
                            }
                        }
                        if (imgUrls['hover_img'] !== '' && imgUrls['hover_img']) {
                            try {
                                const filePath = path.join(__dirname, '..', '..', 'public', oldProduct[0].hover_img);
                                await fs.promises.unlink(filePath);
                            } catch (error) {
                                console.error('Error deleting file:', error.message);
                            }
                        }
                        if (imgUrls['main_img'] !== '' && imgUrls['main_img']) {
                            try {
                                const filePath = path.join(__dirname, '..', '..', 'public', oldDescriptionImageUrls);
                                await fs.promises.unlink(filePath);
                            } catch (error) {
                                console.error('Error deleting file:', error.message);
                            }
                        }
                    });
                } else {
                    // If no new images were uploaded, remove the images field from the update query
                    delete req.body.productImages;
                }

                // let updateQueryParams = [mcID, scID, heading, title, description, price, brandID, highLights, available, status, quantity, features];
                // let updateQueryParams = [mcID, tags, quantityTypes, pincodes, mfg, gst, descitptionDescription, scID, discount, priceBasedOnType, heading, description, rating, price, brandID, highLights, available, status, quantity, features, JSON.stringify(variantSkus), isCODAvailable, dimentions, deliveryCharges, codCharges, sku];
                let updateQueryParams = [mcID, tags, quantityTypes, pincodes, mfg, gst, descitptionDescription, scID, discount, priceBasedOnType, heading, description, rating, price, brandID, highLights, available, status, quantity, features, isCODAvailable, dimentions, deliveryCharges, codCharges, sku];
                // let updateQuery = 'UPDATE products SET mcID = ?, scID = ?, heading = ?, title = ?, description = ?, price = ?,brandID = ?, highLights = ?, available = ?, status = ?, quantity = ?, features = ?';
                let updateQuery = 'UPDATE products SET mcID = ?, tags = ?, quantityTypes = ?, pincodes = ?,mfg=?, gst = ?, description_desc = ?, scID = ?, discount = ?, 	price_based_on_type = ?, heading = ?,  description = ?, rating = ?, price = ?,brandID = ?, highLights = ?, available = ?, status = ?, quantity = ?, features = ?, is_cod_available = ?, dimentions = ?, delivery_charges = ?, cod_charges = ?, sku = ?, user_id = ?';

                if (!req.session?.user?.vendorID) {
                    updateQueryParams.push(vendor_id)
                }
                else {
                    updateQueryParams.push(req.session?.user?.ID)
                }
                if (imgUrls['images'].length > 0) {
                    // If new images were uploaded, include productImages in the update query
                    updateQuery += ', productImages = ?';
                    updateQueryParams.push(JSON.stringify(imgUrls['images']));
                }
                if (imgUrls['main_img']) {
                    // If new images were uploaded, include productImages in the update query
                    updateQuery += ', main_img = ?';
                    updateQueryParams.push(imgUrls['main_img']);
                }
                if (imgUrls['hover_img']) {
                    // If new images were uploaded, include productImages in the update query
                    updateQuery += ', hover_img = ?';
                    updateQueryParams.push(imgUrls['hover_img']);
                }

                updateQuery += ' WHERE id = ?';
                updateQueryParams.push(id);

                // Update product information and image URLs in the database
                pool.query(
                    updateQuery,
                    updateQueryParams,
                    async function (error, result) {
                        if (error) {
                            console.error(error.message);
                            return res.status(500).json({ message: 'Internal server error' });
                        }

                        // Check if any rows were affected by the update
                        if (result.affectedRows > 0) {
                            // Fetch the updated data using the id
                            pool.query('SELECT * FROM products WHERE id = ?', [id], function (selectError, selectResult) {
                                if (selectError) {
                                    console.error(selectError.message);
                                    return res.status(500).json({ message: 'Error fetching updated data' });
                                }
                                // The updated data is available in selectResult
                                return res.json(selectResult[0]);
                            });
                        } else {
                            // If no rows were affected, the record with the given id was not found
                            return res.status(404).json({ message: 'Record not found' });
                        }
                    }
                );
            } catch (error) {
                console.error(error.message);
                return res.status(500).json({ message: 'Internal server error' });
            }
        }
    });
});

// Update a products quantity
router.put('/quantity/:id', async (req, res) => {
    try {
        const { id } = req.params;
        const { boughtQuantity } = req.body;
        if (!boughtQuantity) {
            return res.status(400).json({ message: 'Missing required fields', boughtQuantity: 'Bought Quantity' });
        }

        // First, retrieve the product data to get the list of associated image URLs
        pool.query('SELECT id, quantity FROM products WHERE id = ?', [id], (selectError, selectResult) => {
            if (selectError) {
                console.error(selectError.message);
                return res.status(500).json({ message: 'Internal server error' });
            }

            // Check if any rows were returned by the select operation
            if (selectResult.length === 0) {
                return res.status(404).json({ message: 'Product not found' });
            }

            const product = selectResult[0];
            const currentQuantity = product.quantity;

            if (currentQuantity >= boughtQuantity) {
                const updatedQuantity = currentQuantity - boughtQuantity;

                // Update the quantity in the database
                pool.query('UPDATE products SET quantity = ? WHERE id = ?', [updatedQuantity, id], (updateError, updateResult) => {
                    if (updateError) {
                        console.error(updateError.message);
                        return res.status(500).json({ message: 'Internal server error' });
                    }

                    if (updateResult.affectedRows > 0) {
                        pool.query('SELECT * FROM products WHERE id = ?', [id], function (selectError, selectResult) {
                            if (selectError) {
                                console.error(selectError.message);
                                return res.status(500).json({ message: 'Error fetching updated data' });
                            }
                            // The updated data is available in selectResult
                            return res.json(selectResult[0]);
                        });
                    } else {
                        res.status(500).json({ message: 'Failed to update product quantity' });
                    }
                });
            } else {
                // If the current quantity is less than the quantity to be sold
                res.status(400).json({ message: 'Insufficient quantity available' });
            }
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});

// Delete a products
router.delete('/:id', async (req, res) => {
    try {
        const { id } = req.params;

        // First, retrieve the product data to get the list of associated image URLs
        pool.query('SELECT productImages FROM products WHERE id = ?', [id], async (selectError, productData) => {
            if (selectError) {
                console.error(selectError.message);
                return res.status(500).json({ message: 'Internal server error' });
            }

            if (!productData || productData.length === 0) {
                return res.status(404).json({ message: 'Product not found' });
            }

            // Parse the product images from the database result
            const productImages = JSON.parse(productData[0].productImages);

            // Delete the associated files
            for (const imageUrl of productImages) {
                try {
                    // Construct the file path
                    const filePath = path.join(__dirname, '..', '..', 'public', imageUrl);

                    // Delete the file
                    await fs.promises.unlink(filePath);
                } catch (error) {
                    console.error('Error deleting file:', error.message);
                }
            }

            let images = JSON.stringify([]);

            // Once files are deleted, proceed with deleting the product record from the database
            pool.query('UPDATE products SET is_deleted = 1, productImages = ? WHERE id = ?', [images, id], (deleteError, deleteResult) => {
                if (deleteError) {
                    console.error(deleteError.message);
                    return res.status(500).json({ message: 'Internal server error' });
                }

                // Check if any rows were affected by the delete operation
                if (deleteResult.affectedRows > 0) {
                    res.json({ message: 'Product and associated files deleted successfully' });
                } else {
                    // If no rows were affected, the record with the given id was not found
                    res.status(404).json({ message: 'Product not found' });
                }
            });
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});


// New Code 
// Get products by mcID
router.get('/productsbycategory/:id', async (req, res) => {
    try {
        const { id } = req.params;
        //let statement = 'SELECT *, (price+(price * (tax / 100))) AS price_with_gst FROM productsnew WHERE category = ?  AND status = 1';
        // let statement = 'SELECT *, (price+(price * (tax / 100))) AS price_with_gst FROM productsnew WHERE FIND_IN_SET(?, category) > 0 AND status = 1';
        let statement = 'SELECT productsnew.*, brands.ID as brandid,brands.brandName as brandName,categories.categoryName,subcategory.subCategoryName, (price+(price * (tax / 100))) AS price_with_gst FROM productsnew JOIN brands ON brands.ID = productsnew.brand_id JOIN categories ON categories.ID = productsnew.category JOIN subcategory ON subcategory.ID = productsnew.subcategory WHERE FIND_IN_SET(?, productsnew.category) > 0 AND productsnew.status = 1'
        
        if (req.session?.user?.vendorID) {
            statement = statement + ' AND productsnew.user_id = ' + req.session?.user?.ID
        }
        await pool.query(statement, [id], async function (error, data) {
            if (error) {
                console.error(error.message);
                res.status(500).json({ message: 'Internal server error' });
            };
            for (let product of data) {
                if (product.features) {
                    let featureIds = JSON.parse(product.features);
                    let features = await fetchFeatureData(featureIds);
                    product.featuresData = features;
                }
            }
            res.json(data);
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});


// Get a products by ID
router.get('/byid/:id', async (req, res) => {
    try {
        const { id } = req.params;
        //console.log(id)
        await pool.query('SELECT productsnew.*, brands.ID as brandid,brands.brandName as brandName,vendors.name as vendorName,categories.categoryName,subcategory.subCategoryName, (price+(price * (tax / 100))) AS price_with_gst FROM productsnew JOIN vendors ON vendors.vendorID = productsnew.vendor_id JOIN brands ON brands.ID = productsnew.brand_id JOIN categories ON categories.ID = productsnew.category JOIN subcategory ON subcategory.ID = productsnew.subcategory WHERE productsnew.id = ? AND productsnew.status = 1', [id], async function (error, data) {
            if (error) {
                console.error(error.message);
                res.status(500).json({ message: 'Internal server error' });
            };
            // console.log(data)
            if (data.length > 0) {
                if (data[0].features) {
                    let featureIds = JSON.parse(data[0].features);
                    let features = await fetchFeatureData(featureIds);
                    data[0].featuresData = features;
                }
            }
            res.json(data[0]);
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});


// Get products by mcID
router.get('/category/:id', async (req, res) => {
    try {
        const { id } = req.params;
        const categoryIds = id.split(','); // Split the parameter string into an array of category IDs
        
        let statement = `
            SELECT productsnew.*, 
                   brands.ID as brandid,
                   brands.brandName as brandName,
                   categories.categoryName,
                   subcategory.subCategoryName, 
                   (price+(price * (tax / 100))) AS price_with_gst 
            FROM productsnew 
            JOIN brands ON brands.ID = productsnew.brand_id 
            JOIN categories ON categories.ID = productsnew.category 
            JOIN subcategory ON subcategory.ID = productsnew.subcategory 
            WHERE productsnew.category IN (?)
            AND productsnew.status = 1
        `;

    //     let statement = `
    //     SELECT productsnew.*, 
    //            brands.ID as brandid,
    //            brands.brandName as brandName,
    //            categories.categoryName,
    //            subcategory.subCategoryName, 
    //            (price+(price * (tax / 100))) AS price_with_gst 
    //     FROM productsnew 
    //     JOIN brands ON brands.ID = productsnew.brand_id 
    //     JOIN categories ON categories.ID = productsnew.category 
    //     JOIN subcategory ON subcategory.ID = productsnew.subcategory
    //     WHERE FIND_IN_SET(?, productsnew.category)
    //     AND productsnew.is_deleted = 0
    //     AND productsnew.status = 1
    // `;

        if (req.session?.user?.vendorID) {
            statement += ' AND productsnew.user_id = ' + req.session?.user?.ID;
        }
        
        await pool.query(statement, [categoryIds], async function (error, data) {
            if (error) {
                console.error(error.message);
                return res.status(500).json({ message: 'Internal server error' });
            }
            for (let product of data) {
                if (product.features) {
                    let featureIds = JSON.parse(product.features);
                    let features = await fetchFeatureData(featureIds);
                    product.featuresData = features;
                }
            }
            res.json(data);
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});


// Get products by mcID
router.get('/relatedproducts/:id', async (req, res) => {
    try {
        const { id } = req.params;
        // const categoryIds = id.split(','); // Split the parameter string into an array of category IDs
        
        let statement = `
            SELECT productsnew.*, 
                   brands.ID as brandid,
                   brands.brandName as brandName,
                   categories.categoryName,
                   subcategory.subCategoryName, 
                   (price+(price * (tax / 100))) AS price_with_gst 
            FROM productsnew 
            JOIN brands ON brands.ID = productsnew.brand_id 
            JOIN categories ON categories.ID = productsnew.category 
            JOIN subcategory ON subcategory.ID = productsnew.subcategory 
            WHERE productsnew.category = (?)
            AND productsnew.status = 1
        `;

    //     let statement = `
    //     SELECT productsnew.*, 
    //            brands.ID as brandid,
    //            brands.brandName as brandName,
    //            categories.categoryName,
    //            subcategory.subCategoryName, 
    //            (price+(price * (tax / 100))) AS price_with_gst 
    //     FROM productsnew 
    //     JOIN brands ON brands.ID = productsnew.brand_id 
    //     JOIN categories ON categories.ID = productsnew.category 
    //     JOIN subcategory ON subcategory.ID = productsnew.subcategory
    //     WHERE FIND_IN_SET(?, productsnew.category)
    //     AND productsnew.is_deleted = 0
    //     AND productsnew.status = 1
    // `;

        if (req.session?.user?.vendorID) {
            statement += ' AND productsnew.user_id = ' + req.session?.user?.ID;
        }
        
        await pool.query(statement, [id], async function (error, data) {
            if (error) {
                console.error(error.message);
                return res.status(500).json({ message: 'Internal server error' });
            }
            for (let product of data) {
                if (product.features) {
                    let featureIds = JSON.parse(product.features);
                    let features = await fetchFeatureData(featureIds);
                    product.featuresData = features;
                }
            }
            res.json(data);
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});


//End

module.exports = router;
