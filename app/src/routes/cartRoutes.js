const express = require('express');
const router = express.Router();
const bcrypt = require('bcrypt');
const pool = require('../database');

const checkCustomerExists = async (userId) => {
    return new Promise((resolve, reject) => {
        pool.query('SELECT * FROM customer WHERE id = ?', [userId], function (error, result) {
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
        pool.query('SELECT * FROM productsnew WHERE ID = ? AND status = 1', [productId], function (error, result) {
            if (error) {
                reject(error);
            } else {
                resolve(result.length > 0);
            }
        });
    });
};

const checkProductAlreadyInCart = async (productId, userId) => {
    return new Promise((resolve, reject) => {
        pool.query('SELECT * FROM cart WHERE customerID = ? AND productID = ?', [userId, productId], function (error, result) {
            if (error) {
                reject(error);
            } else {
                resolve(result.length > 0);
            }
        });
    });
};




// Create a new cart
router.post('/', async (req, res) => {
    try {
        const { productId, userId, product_type, product_attributes, status } = req.body;
        const missingFields = {};
        if (!productId) missingFields.productId = 'Product Id';
        if (!status) missingFields.status = 'Status';
        if (!userId) missingFields.userId = 'User Id';

        if (Object.keys(missingFields).length > 0) {
            return res.status(400).json({ message: 'Missing required fields', missingFields });
        }

        const customerExists = await checkCustomerExists(userId);
        if (!customerExists) {
            return res.status(400).json({ message: 'User does not exist' });
        }

        const productExists = await checkProductExists(productId);
        if (!productExists) {
            return res.status(400).json({ message: 'Product does not exist' });
        }

        const checkIfTheProductAlreadyInCart = await checkProductAlreadyInCart(productId, userId);
        if (checkIfTheProductAlreadyInCart) {
            return res.status(400).json({ message: 'Product already added to cart' });
        }
        
        let productAttributesString = null;
        if(product_type === 'variable'){
            // Convert product_attributes object to JSON string
            productAttributesString = JSON.stringify(product_attributes);
        }

        await pool.query('INSERT INTO cart (customerID, productID, product_type, product_attributes, status) VALUES (?, ?, ?, ?, ?)',
            [userId, productId, product_type, productAttributesString, status], async function (error, result) {
                if (error) {
                    console.error(error.message);
                    return res.status(500).json({ message: 'Internal server error' });
                }
                const insertedId = result.insertId;
                await pool.query('SELECT * FROM cart WHERE id = ?', [insertedId], function (error, data) {
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


// Create a new buy cart
router.post('/buyitem', async (req, res) => {
    try {
        const { productId, userId, status } = req.body;
        const missingFields = {};
        if (!productId) missingFields.productId = 'Product Id';
        if (!status) missingFields.status = 'Status';
        if (!userId) missingFields.userId = 'User Id';

        if (Object.keys(missingFields).length > 0) {
            return res.status(400).json({ message: 'Missing required fields', missingFields });
        }

        const customerExists = await checkCustomerExists(userId);
        if (!customerExists) {
            return res.status(400).json({ message: 'User does not exist' });
        }

        const productExists = await checkProductExists(productId);
        if (!productExists) {
            return res.status(400).json({ message: 'Product does not exist' });
        }

        const checkIfTheProductAlreadyInCart = await checkProductAlreadyInCart(productId, userId);
        if (checkIfTheProductAlreadyInCart) {
            return res.status(400).json({ message: 'Product already added to cart' });
        }

        await pool.query('INSERT INTO buy_cart (customerID, productID, status) VALUES (?, ?, ?)',
            [userId, productId, status], async function (error, result) {
                if (error) {
                    console.error(error.message);
                    return res.status(500).json({ message: 'Internal server error' });
                }
                const insertedId = result.insertId;
                await pool.query('SELECT * FROM buy_cart WHERE id = ?', [insertedId], function (error, data) {
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

// Create a new cart
router.post('/addcart', async (req, res) => {
    try {
        const { productIds, userId, status } = req.body;
        const missingFields = {};
        // if (!productIds && !Array.isArray(productIds)) missingFields.productIds = 'Product Ids';
        if (!status) missingFields.status = 'Status';
        if (!userId) missingFields.userId = 'User Id';

        if (Object.keys(missingFields).length > 0) {
            return res.status(400).json({ message: 'Missing required fields', missingFields });
        }

        if (productIds.length === 0) {
            return res.status(400).json({ message: 'Product ID array is empty' });
        }

        const productNotInCart = [];
        for (const id of productIds) {
            const productExists = await checkProductExists(id);
            const alreadyInCart = await checkProductAlreadyInCart(id, userId);
            if (!productExists) {
                // return res.status(400).json({ message: `Product ${id} does not exist` });
            } else if (alreadyInCart) {
                // return res.status(400).json({ message: `Product ${id} is already added to cart` });
            } else {
                productNotInCart.push(id);
            }
        }

        // Insert each product into the cart
        const insertPromises = productNotInCart.map(id => {
            return new Promise((resolve, reject) => {
                pool.query('INSERT INTO cart (customerID, productID, status) VALUES (?, ?, ?)',
                    [userId, id, status], (error, result) => {
                        if (error) {
                            console.error(error.message);
                            reject(error);
                        } else {
                            resolve(result.insertId);
                        }
                    });
            });
        });

        // Execute all insertion queries
        const insertedIds = await Promise.all(insertPromises);

        // Fetch details of the inserted rows and send the response
        const fetchedDataPromises = insertedIds.map(insertedId => {
            return new Promise((resolve, reject) => {
                pool.query('SELECT * FROM cart WHERE id = ?', [insertedId], (error, data) => {
                    if (error) {
                        console.error(error.message);
                        reject(error);
                    } else {
                        resolve(data[0]);
                    }
                });
            });
        });

        const insertedData = await Promise.all(fetchedDataPromises);

        res.json(insertedData);
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});

// Get all cart items
router.get('/', async (req, res) => {
    try {
        await pool.query('SELECT cart.customerID, cart.productID, customer.*, productsnew.* FROM cart JOIN customer ON cart.customerID = customer.ID JOIN productsnew ON cart.productID = productsnew.ID WHERE productsnew.status = 1', function (error, result, fields) {
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

// Get a cart by ID
router.get('/:id', async (req, res) => {
    try {
        const { id } = req.params;
        await pool.query('SELECT * FROM cart WHERE id = ?', [id], function (error, data) {
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

// Update a cart
router.put('/:id', async (req, res) => {
    try {
        const { id } = req.params;
        const { userId, productId, status } = req.body;
        const missingFields = {};
        if (!productId) missingFields.productId = 'Product Id';
        if (!userId) missingFields.userId = 'User Id';
        if (!status) missingFields.status = 'Status';

        if (Object.keys(missingFields).length > 0) {
            return res.status(400).json({ message: 'Missing required fields', missingFields });
        }


        const customerExists = await checkCustomerExists(userId);
        if (!customerExists) {
            return res.status(400).json({ message: 'User does not exist' });
        }

        const productExists = await checkProductExists(productId);
        if (!productExists) {
            return res.status(400).json({ message: 'Product does not exist' });
        }

        await pool.query(
            // 'UPDATE cart SET name = ?, password = ?, email = ?, mobile = ?, user_type = ?, status = ? WHERE id = ?',
            'UPDATE cart SET userID = ?,  productID = ?,  status = ? WHERE id = ?',
            // [name, hashedPassword, email, mobile, user_type, status, id],
            [userId, productId, status, id],
            async function (error, result) {
                if (error) {
                    console.error(error.message);
                    return res.status(500).json({ message: 'Internal server error' });
                }
                res.json({ message: 'Cart updated successfully' });
            }
        );
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});

// Get the last order of a user by user ID
router.get('/user/:userId', async (req, res) => {
    try {
        const { userId } = req.params;

        await pool.query('SELECT cart.customerID, cart.productID, cart.ID as cartID, cart.quantity as qty,cart.product_type as cart_product_type,cart.product_attributes as product_attributes,customer.*, productsnew.*, (productsnew.price * (productsnew.tax / 100)) AS price_with_gst FROM cart JOIN customer ON cart.customerID = customer.ID JOIN productsnew ON cart.productID = productsnew.ID WHERE cart.customerID = ? AND productsnew.status = 1', [userId], function (error, data) {
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
            // 'UPDATE cart SET name = ?, password = ?, email = ?, mobile = ?, user_type = ?, status = ? WHERE id = ?',
            'UPDATE cart SET status = ? WHERE id = ?',
            // [name, hashedPassword, email, mobile, user_type, status, id],
            [status, id], function (error, result) {
                if (error) {
                    console.error(error.message);
                    return res.status(500).json({ message: 'Internal server error' });
                }
                if (result.affectedRows === 0) {
                    return res.status(404).json({ message: 'Cart not found' });
                }
                res.json({ message: 'Cart deleted successfully' });
            });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});

// Delete a cart
router.delete('/:id', async (req, res) => {
    try {
        const { id } = req.params;

        await pool.query('DELETE FROM cart WHERE ID = ?', [id], function (error, result) {
            if (error) {
                console.error(error.message);
                return res.status(500).json({ message: 'Internal server error' });
            }
            if (result.affectedRows === 0) {
                return res.status(404).json({ message: 'Cart Item not found' });
            }
            res.json({ message: 'Customer deleted successfully' });
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});


router.put('/cartupdate/:id', async (req, res) => {
    try {
        const { id } = req.params;
        const { userId, productQty } = req.body;
        // const missingFields = {};
        // if (!status) missingFields.status = 'Status';

        // if (Object.keys(missingFields).length > 0) {
        //     return res.status(400).json({ message: 'Missing required fields', missingFields });
        // }

        await pool.query(
            // 'UPDATE cart SET name = ?, password = ?, email = ?, mobile = ?, user_type = ?, status = ? WHERE id = ?',
            'UPDATE cart SET quantity = ? WHERE productID = ? AND customerID = ?',
            // [name, hashedPassword, email, mobile, user_type, status, id],
            [productQty, id, userId], function (error, result) {
                if (error) {
                    console.error(error.message);
                    return res.status(500).json({ message: 'Internal server error' });
                }
                if (result.affectedRows === 0) {
                    return res.status(404).json({ message: 'Cart not found' });
                }
                res.json({ message: 'Cart Updated successfully' });
            });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});


//new
// Get the last order of a user by user ID
router.get('/customer/:userId', async (req, res) => {
    try {
        const { userId } = req.params;

        await pool.query('SELECT cart_products.*,customer.*, productsnew.ID,productsnew.shipping_charges, (productsnew.price * (productsnew.tax / 100)) AS price_with_gst FROM cart_products JOIN customer ON cart_products.customerID = customer.ID JOIN productsnew ON cart_products.productID = productsnew.ID WHERE cart_products.customerID = ? AND productsnew.status = 1', [userId], function (error, data) {
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

// Get the last order of a user by user ID
router.get('/customerinfo/:userId', async (req, res) => {
    try {
        const { userId } = req.params;

        await pool.query('SELECT buy_cart.customerID, buy_cart.productID, buy_cart.ID as cartID, buy_cart.quantity as qty,customer.*, productsnew.ID,productsnew.product_id,productsnew.product_type,productsnew.title,productsnew.quantity,productsnew.price,productsnew.max_price,productsnew.sale_price,productsnew.main_img,productsnew.product_images,productsnew.shipping_charges, (productsnew.price * (productsnew.tax / 100)) AS price_with_gst FROM cart JOIN customer ON buy_cart.customerID = customer.ID JOIN productsnew ON buy_cart.productID = productsnew.ID WHERE buy_cart.customerID = ? AND productsnew.status = 1', [userId], function (error, data) {
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


const checkProductExistorNotInCart = async (productId, userId, attributes) => {
    return new Promise((resolve, reject) => {
        pool.query('SELECT * FROM cart_products WHERE customerID = ? AND productID = ? AND attributes = ?', [userId, productId, JSON.stringify(attributes)], function (error, result) {
            if (error) {
                reject(error);
            } else {
                resolve(result.length > 0);
            }
        });
    });
};


// Add Item to Cart
router.post('/additem', async (req, res) => {
    try {
        const { productId, vendor_id, productTitle, productImage, productType, attributes, image, price, salePrice, quantity, sku, cod_charges, shipping_charges, others, userId } = req.body;
        const missingFields = {};
        if (!productId) missingFields.productId = 'Product Id';
        // if (!status) missingFields.status = 'Status';
        if (!userId) missingFields.userId = 'User Id';

        if (Object.keys(missingFields).length > 0) {
            return res.status(400).json({ message: 'Missing required fields', missingFields });
        }

        const customerExists = await checkCustomerExists(userId);
        if (!customerExists) {
            return res.status(400).json({ message: 'User does not exist' });
        }

        const productExists = await checkProductExists(productId);
        if (!productExists) {
            return res.status(400).json({ message: 'Product does not exist' });
        }

        // for( attr of attributes){
        //     console.log(attr)
        // }
        // for (const id of productIds) {
        //     const productExists = await checkProductExists(id);
        //     const alreadyInCart = await checkProductAlreadyInCart(id, userId);
        //     if (!productExists) {
        //         // return res.status(400).json({ message: `Product ${id} does not exist` });
        //     } else if (alreadyInCart) {
        //         // return res.status(400).json({ message: `Product ${id} is already added to cart` });
        //     } else {
        //         productNotInCart.push(id);
        //     }
        // }

        const checkIfTheProductAlreadyInCart = await checkProductExistorNotInCart(productId, userId, attributes);
        if (checkIfTheProductAlreadyInCart) {
            return res.status(400).json({ message: 'Product already added to cart' });
        }
        
        // Convert attributes to JSON string
        const attributesString = JSON.stringify(attributes);
        const othersString = JSON.stringify(others);
        const salePrice1 = salePrice==="" ? 0 : salePrice;

        // const query = `INSERT INTO cart_products (productID, customerID, vendorID, productTitle, productImage, productType, attributes, image, price, salePrice, quantity, sku, others) VALUES (${productId}, ${userId}, ${vendor_id}, '${productTitle}', '${productImage}', '${productType}', '${attributesString}', '${image}', ${price}, ${salePrice1}, ${quantity}, '${sku}', '${othersString}')`;

        // console.log("Query:", query);
        
        // const params = [
        //     productId,
        //     userId,
        //     vendor_id,
        //     productTitle,
        //     productImage,
        //     productType,
        //     attributesString,
        //     image,
        //     price,
        //     salePrice,
        //     quantity,
        //     sku,
        //     othersString
        // ];

        // console.log("Query:", query);
        // console.log("Parameters:", params);
        //${productId}, ${userId}, ${vendor_id}, '${productTitle}', '${productImage}', '${productType}', '${attributesString}', '${image}', ${price}, ${salePrice1}, ${quantity}, '${sku}', '${othersString}'

        await pool.query(`INSERT INTO cart_products (productID, customerID, vendorID, productTitle, productImage, productType, attributes, image, price, salePrice, quantity, sku, cod_charges, shipping_charges, others) VALUES (${productId}, ${userId}, ${vendor_id}, '${productTitle}', '${productImage}', '${productType}', '${attributesString}', '${image}', ${price}, ${salePrice1}, ${quantity}, '${sku}', '${cod_charges}', '${shipping_charges}', '${othersString}')`, async function (error, result) {
                if (error) {
                    console.error(error.message);
                    return res.status(500).json({ message: 'Internal server error' });
                }
                const insertedId = result.insertId;
                await pool.query('SELECT * FROM cart_products WHERE id = ?', [insertedId], function (error, data) {
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

// Get the cart items of a user by user ID
router.get('/items/:userId', async (req, res) => {
    try {
        const { userId } = req.params;

        await pool.query('SELECT cart_products.*, productsnew.ID as ProductID, productsnew.variables, productsnew.variables_quantity,vendors.name as vendorName, (productsnew.price * (productsnew.tax / 100)) AS price_with_gst FROM cart_products JOIN vendors ON cart_products.vendorID = vendors.vendorID JOIN productsnew ON cart_products.productID = productsnew.ID WHERE cart_products.customerID = ? AND productsnew.status = 1', [userId], function (error, data) {
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

router.put('/cartproductupdate/:id', async (req, res) => {
    try {
        const { id } = req.params;
        const { userId, productQty } = req.body;
        
        await pool.query(
            // 'UPDATE cart SET name = ?, password = ?, email = ?, mobile = ?, user_type = ?, status = ? WHERE id = ?',
            'UPDATE cart_products SET quantity = ? WHERE ID = ? AND customerID = ?',
            // [name, hashedPassword, email, mobile, user_type, status, id],
            [productQty, id, userId], function (error, result) {
                if (error) {
                    console.error(error.message);
                    return res.status(500).json({ message: 'Internal server error' });
                }
                if (result.affectedRows === 0) {
                    return res.status(404).json({ message: 'Cart not found' });
                }
                res.json({ message: 'Cart Updated successfully' });
            });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});


// Delete a cart
router.delete('/deleteItem/:id', async (req, res) => {
    try {
        const { id } = req.params;

        await pool.query('DELETE FROM cart_products WHERE ID = ?', [id], function (error, result) {
            if (error) {
                console.error(error.message);
                return res.status(500).json({ message: 'Internal server error' });
            }
            if (result.affectedRows === 0) {
                return res.status(404).json({ message: 'Cart Item not found' });
            }
            res.json({ message: 'Customer deleted successfully' });
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});


// Add Buy Cart Item

// Add Item to Cart dicrect buy product
// router.post('/addbuyitem', async (req, res) => {
//     try {
//         console.log(req.body)
//         const { productId, vendor_id, productTitle, productImage, productType, attributes, image, price, salePrice, quantity, sku,cod_charges, shipping_charges, others, userId } = req.body;
//         const missingFields = {};
//         if (!productId) missingFields.productId = 'Product Id';
//         // if (!status) missingFields.status = 'Status';
//         if (!userId) missingFields.userId = 'User Id';

//         if (Object.keys(missingFields).length > 0) {
//             return res.status(400).json({ message: 'Missing required fields', missingFields });
//         }

//         const customerExists = await checkCustomerExists(userId);
//         if (!customerExists) {
//             return res.status(400).json({ message: 'User does not exist' });
//         }

//         const productExists = await checkProductExists(productId);
//         if (!productExists) {
//             return res.status(400).json({ message: 'Product does not exist' });
//         }

//         // Convert attributes to JSON string
//         const attributesString = JSON.stringify(attributes);
//         const othersString = JSON.stringify(others);
//         const salePrice1 = salePrice==="" ? 0 : salePrice;

//         // await pool.query('DELETE FROM buy_cart_products WHERE customerID = ?', [userId]);
        
//         await pool.query(`INSERT INTO buy_cart_products (productID, customerID, vendorID, productTitle, productImage, productType, attributes, image, price, salePrice, quantity, sku, cod_charges, shipping_charges, others) VALUES (${productId}, ${userId}, ${vendor_id}, '${productTitle}', '${productImage}', '${productType}', '${attributesString}', '${image}', ${price}, ${salePrice1}, ${quantity}, '${sku}', '${cod_charges}', '${shipping_charges}', '${othersString}')`, async function (error, result) {
//                 if (error) {
//                     console.error(error.message);
//                     return res.status(500).json({ message: 'Internal server error' });
//                 }
//                 const insertedId = result.insertId;
//                 await pool.query('SELECT * FROM buy_cart_products WHERE id = ?', [insertedId], function (error, data) {
//                     if (error) {
//                         console.error(error.message);
//                         return res.status(500).json({ message: 'Internal server error' });
//                     }
//                     if (data.length === 0) {
//                         return res.status(404).json({ message: 'Customer not found' });
//                     }
//                     res.json(data[0]);
//                 });
//             });
//     } catch (error) {
//         console.error(error.message);
//         res.status(500).json({ message: 'Internal server error' });
//     }
// });

router.post('/addbuyitem', async (req, res) => {
    try {
        console.log(req.body);
        const { productId, vendor_id, productTitle, productImage, productType, attributes, image, price, salePrice, quantity, sku, cod_charges, shipping_charges, others, userId } = req.body;
        const missingFields = {};
        if (!productId) missingFields.productId = 'Product Id';
        if (!userId) missingFields.userId = 'User Id';

        if (Object.keys(missingFields).length > 0) {
            return res.status(400).json({ message: 'Missing required fields', missingFields });
        }

        

        // Convert attributes to JSON string
        const attributesString = JSON.stringify(attributes);
        const othersString = JSON.stringify(others);
        const salePrice1 = salePrice === "" ? 0 : salePrice;

        // Delete existing records for the user and product
        await pool.query('DELETE FROM buy_cart_products WHERE customerID = ?', [userId], async function (error, deleteResult) {
            if (error) {
                console.error(error.message);
                return res.status(500).json({ message: 'Internal server error' });
            }

            // Insert new record
            await pool.query(`INSERT INTO buy_cart_products (productID, customerID, vendorID, productTitle, productImage, productType, attributes, image, price, salePrice, quantity, sku, cod_charges, shipping_charges, others) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`, [productId, userId, vendor_id, productTitle, productImage, productType, attributesString, image, price, salePrice1, quantity, sku, cod_charges, shipping_charges, othersString], async function (error, insertResult) {
                if (error) {
                    console.error(error.message);
                    return res.status(500).json({ message: 'Internal server error' });
                }
                const insertedId = insertResult.insertId;

                // Retrieve inserted record
                await pool.query('SELECT * FROM buy_cart_products WHERE id = ?', [insertedId], function (error, data) {
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
        });
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ message: 'Internal server error' });
    }
});



// Get the cart items of a user by user ID
router.get('/buyitem/:userId', async (req, res) => {
    try {
        const { userId } = req.params;

        await pool.query('SELECT buy_cart_products.*, productsnew.ID as ProductID, productsnew.variables, productsnew.variables_quantity, vendors.name as vendorName, (productsnew.price * (productsnew.tax / 100)) AS price_with_gst FROM buy_cart_products JOIN vendors ON buy_cart_products.vendorID = vendors.vendorID JOIN productsnew ON buy_cart_products.productID = productsnew.ID WHERE buy_cart_products.customerID = ? AND productsnew.status = 1', [userId], function (error, data) {
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


//End Buy Cart Item

//

module.exports = router;
