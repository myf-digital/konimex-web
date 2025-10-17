#!/bin/bash
/usr/bin/curl -kv https://par-web.product-act.com/index.php/api_v1/call_pjp_daily -o /apps/par-web/application/logs/pjp/log_pjp-$(date +"%b-%d_%y").log
##/usr/bin/curl -kv https://par-web.product-act.com/index.php/api_v1/generate_stock_doi -o /apps/par-web/application/logs/doi/log_doi-$(date +"%b-%d_%y").log
