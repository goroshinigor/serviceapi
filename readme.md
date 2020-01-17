# admin url:
http://v2service.api/v2/s_admin
# Doctrine update DB
php bin/console doctrine:schema:update --force
# Testing with Postman
   Pre-request Script
   ```
   var key = '7ac6afc5d17f8e5fe37a568288202574d7dc5ed2f973e7f7f4132a3419e9d766b5b8ea2d73d259680a30ab877edfcc045980d6f8c890456a8bdbc23cbebb344110af2ae9fd389fa47a84f2c5e014e43ac79b4ec89d89f74ce49e945574c355a664d12f8b5b6946ed44dc2b0a76416377a2e6f98a2379b210504c730255707624adf78633f427c207fc49e872ff7f652bdc0c28870cfc9126c0a8902c6a269edbed429276cbc98099459fb3b34825f5388802b37f650f611592c49ab49281f44e8c0100472602a8510447f595110cbeec59e8f1d3107d0bffcfb16d231b0164b37ad07002c440ac5510e0df621f1dfbe130199235806f13caf4e142492dff1719';
   var moment = require('moment');
   var dateVar = moment().format("YYYY-MM-DD h:mm:ss");
   var strForSign = request.data+key;
   strForSign = strForSign.replace('{{sign}}', '');
   strForSign = strForSign.replace('{{datetime}}', dateVar);
   var sign = CryptoJS.enc.Hex.stringify(CryptoJS.SHA1(strForSign));
   pm.environment.set("sign",sign);
   pm.environment.set("datetime",dateVar);
   ```
   Request body
   ```
   {
   "method":"filials_list",
   "login":"test",
   "sign":"{{sign}}",
   "datetime":"{{datetime}}"
   }
   ```


# mysql database VM Ubuntu server 18 install 
sudo apt purge mysql* && 
sudo rm -rf /etc/mysql /var/lib/mysql && 
sudo apt autoremove && 
sudo apt autoclean && 
sudo apt install mysql-server && 
sudo mysql