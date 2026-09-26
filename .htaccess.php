RewriteEngine On
RewriteRule ^laptop_parts/?$ laptop_parts.php [NC,L]
RewriteRule ^laptops_part/([0-9]+)/?$ laptop_parts.php?id=$1 [NC,L]
SetEnvIf Authorization .+ HTTP_AUTHORIZATION=$0
