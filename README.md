# tinywebdb-php
a TinyWebDB implementation based on PHP and textfile. No database is required.

###
### This is a web service for use with App
### Inventor for Android (<http://appinventor.googlelabs.com>)
### This particular service stores and retrieves tag-value pairs 
### using the protocol necessary to communicate with the TinyWebDB
### component of an App Inventor app.
###


# TinyWebDB Protocol:  

|    Action        |URL                      |Post Parameters  |Response                          |
|------------------|-------------------------|-----------------|----------------------------------|
|    Get Value     |{ServiceURL}/getvalue    |tag              |JSON: ["VALUE","{tag}", {value}]  |
|    Store A Value |{ServiceURL}/storeavalue |tag,value        |JSON: ["STORED", "{tag}", {value}]|

# Fertures:
- TinyWebDB API 
    - handle storevalue request, then save to a textfile. 
    - handle getvalue request and return content from the textfile. 
- Test Form: 
    - send storevalue request 
    - send getvalue request 
- Tag View
    - List all tags
    - The tags link to getvalue API
- Log tail viewer 
    - Daily log file 
    - last 20 lines view 

# Install
1) create an Apache virtual host 
2) Enable .htaccess. (set AllowOverride to All)
3) clone all files to virture host root. 

# Test site URL :
- http://tinywebdb.cf/
