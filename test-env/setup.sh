#!/bin/sh
set -e
WP="docker compose exec -T cli wp --path=/var/www/html --allow-root"

echo "== Waiting for DB/WordPress files =="
until docker compose exec -T cli wp --path=/var/www/html --allow-root core version --extra 2>/dev/null; do
  sleep 2
done

if ! $WP core is-installed 2>/dev/null; then
  echo "== Installing WordPress core =="
  $WP core install \
    --url="http://localhost:8099" \
    --title="Rescate Vertical" \
    --admin_user="admin" \
    --admin_password="admin12345" \
    --admin_email="test@rescatevertical.local" \
    --skip-email
fi

echo "== Activating theme =="
$WP theme activate rescate-vertical

echo "== Activating plugin =="
$WP plugin activate rescate-vertical-tools

echo "== Setting permalink structure =="
$WP rewrite structure '/%postname%/' --hard
$WP rewrite flush --hard

create_page () {
  SLUG=$1
  TITLE=$2
  TEMPLATE=$3
  EXISTING=$($WP post list --post_type=page --name="$SLUG" --field=ID --format=csv || true)
  if [ -z "$EXISTING" ]; then
    $WP post create --post_type=page --post_title="$TITLE" --post_name="$SLUG" --post_status=publish --page_template="$TEMPLATE" --porcelain
  else
    echo "$EXISTING"
  fi
}

echo "== Creating section pages =="
ID_QUEES=$(create_page "que-es" "Qué es el rescate vertical" "page-templates/template-que-es.php")
ID_FISICA=$(create_page "fisica" "Física del rescate" "page-templates/template-fisica.php")
ID_TECNICAS=$(create_page "tecnicas" "Técnicas" "page-templates/template-tecnicas.php")
ID_EQUIPOS=$(create_page "equipos" "Equipos" "page-templates/template-equipos.php")
ID_PROTOCOLOS=$(create_page "protocolos" "Protocolos y normativas" "page-templates/template-protocolos.php")
ID_PRACTICAR=$(create_page "practicar" "Practicar en digital" "page-templates/template-practicar.php")

echo "IDs: que-es=$ID_QUEES fisica=$ID_FISICA tecnicas=$ID_TECNICAS equipos=$ID_EQUIPOS protocolos=$ID_PROTOCOLOS practicar=$ID_PRACTICAR"

echo "== Creating navigation menu =="
MENU_EXISTS=$($WP menu list --fields=term_id,name --format=csv | grep -c "Menu principal" || true)
if [ "$MENU_EXISTS" = "0" ]; then
  MENU_ID=$($WP menu create "Menu principal" --porcelain)
  $WP menu location assign "$MENU_ID" primary
  $WP menu item add-custom "$MENU_ID" "Inicio" "http://localhost:8099/"
  $WP menu item add-post "$MENU_ID" "$ID_QUEES"
  $WP menu item add-post "$MENU_ID" "$ID_FISICA"
  $WP menu item add-post "$MENU_ID" "$ID_TECNICAS"
  $WP menu item add-post "$MENU_ID" "$ID_EQUIPOS"
  $WP menu item add-post "$MENU_ID" "$ID_PROTOCOLOS"
  $WP menu item add-post "$MENU_ID" "$ID_PRACTICAR"
else
  echo "Menu already exists, skipping."
fi

echo "== Done =="
$WP option get template
$WP option get stylesheet
$WP plugin list
$WP post list --post_type=page --fields=ID,post_title,post_name,page_template
