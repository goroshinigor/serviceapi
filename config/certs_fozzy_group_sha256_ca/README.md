Current directory contains fozzy certificates(pem and crt)
that should be installed in your system
Ubuntu installation
1) copy *.crt files to /usr/local/share/ca-certificate
2) sudo update-ca-certificates

in case if your system refuses the crt certificates 
you can re generate it from *.pem

openssl x509 -in certificate_name.pem -inform PEM -out certificate_name.crt
