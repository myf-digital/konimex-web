#!/bin/bash

/usr/bin/curl -kv https://par-web.product-act.com/index.php/api_v1/call_recon_available_sku_last3months -o /apps/par_web/application/logs/recon/log_recon_available_sku_-$(date +"%b-%d_%y").log
/usr/bin/curl -kv https://par-web.product-act.com/index.php/api_v1/call_available_sku_last3months_period -o /apps/par_web/application/logs/recon/log_available_sku_last3months_period_-$(date +"%b-%d_%y").log
