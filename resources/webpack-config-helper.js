//watches for changes in config.yml
const fs = require('fs');
const path = require('path');
const configFilePath = path.join(__dirname, '../config/config.yml')
const filePath = path.join(__dirname,'tailwind.config.js');
console.log("loaded");
try {
  fs.watchFile(configFilePath, (curr,prev) => {
    console.log("watch Trigger");
    const time = new Date();
    fs.utimesSync(filePath, time, time);  
  })
  fs.watchFile(path.join(__dirname,'../cache/whitelist.yml'), (curr,prev) => {
    console.log("watch Trigger");
    const time = new Date();
    fs.utimesSync(filePath, time, time);  
  })
} catch (err) {
  console.log(err)
  // fs.closeSync(fs.openSync(filePath, 'w'));
}