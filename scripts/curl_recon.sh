#!/bin/bash
/usr/bin/curl -kv https://par-web.product-act.com/index.php/api_v1/call_recon_fr_oos -o /apps/par_web/application/logs/recon/log_recon-$(date +"%b-%d_%y").log
