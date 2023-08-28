#!/bin/sh

#~ make new workfolder & get tools and configs
[ ! -d /root/monospot ] && mkdir /root/monospot
cd /root/monospot
[ ! -e $PWD/monospot-setup.conf ] && { echo "\"$PWD/monospot-setup.conf\" dosyasi bulunamadi, git uzerinden cekilecek..."; fetch https://raw.githubusercontent.com/monobilisim/monospot/master/monospot-setup.conf; echo "\"$PWD/monospot-setup.conf\"" dosyasini editor araciligi ile duzenledikten sonra \"sh monospot-setup.sh\" komutu ile bu scripti tekrardan calistirin; exit 0; }
. $PWD/monospot-setup.conf

#~ install monospot
[ -d /usr/local/captiveportal/monospot.bak ] && rm -rf /usr/local/captiveportal/monospot.bak
[ -d /usr/local/captiveportal/monospot     ] && mv /usr/local/captiveportal/monospot /usr/local/captiveportal/monospot.bak
[ -d /usr/local/www/monospot               ] && rm /usr/local/www/monospot
fetch https://github.com/monobilisim/monospot/archive/master.zip -o $PWD/monospot.zip
unzip -d /usr/local/captiveportal $PWD/monospot.zip
mv /usr/local/captiveportal/monospot-master /usr/local/captiveportal/monospot
ln -sf /usr/local/captiveportal/monospot /usr/local/www/

#~ install logsigner
fetch https://raw.githubusercontent.com/monobilisim/pfsense-5651/master/logsigner-setup.sh
env FROM_MONOSPOT=true sh logsigner-setup.sh
