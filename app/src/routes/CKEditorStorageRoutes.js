const express = require('express');
const router = express.Router();
const multer  = require('multer');
const path = require('path');
const fs = require('fs');

// Create uploads directory if it doesn't exist
const uploadDir = path.join(__dirname, 'uploads');
if (!fs.existsSync(uploadDir)) {
  fs.mkdirSync(uploadDir);
}

// Configure multer to handle image uploads
const storage = multer.diskStorage({
  destination: function (req, file, cb) {
    cb(null, './uploads/') // Destination directory for uploaded files
  },
  filename: function (req, file, cb) {
    console.log(req.file);
    cb(null, Date.now() + path.extname(file.originalname)) // Unique filename
  }
});

const upload = multer({ storage: storage });

// POST endpoint to handle image uploads
router.post('/', upload.single('image'), (req, res) => {
  // Here you can handle the uploaded image, save it to your desired location,
  // and send back the URL of the uploaded image
  const imageUrl = 'https://yourdomain.com/uploads/' + req.file.filename;
  res.json({ url: imageUrl });
});

module.exports = router;