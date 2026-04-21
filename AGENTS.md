# Istruzioni per lo Sviluppo: Plugin "Configuratore Vernici B2B"

## 1. Contesto e Obiettivo
Sito e-commerce B2B (WordPress + Flatsome + WooCommerce in modalità Catalogo).
Obiettivo: Sviluppare un plugin custom basato sul **WordPress Plugin Boilerplate (WPPB)** per la Lead Generation. 
Il plugin mostra uno shortcode `[configuratore_vernici]` con un form che suggerisce la vernice ideale, calcola i litri necessari e propone un upsell per la consegna gratuita, acquisendo infine l'email del cliente per il preventivo.

I settori focus B2B (Categorie WooCommerce) sono 4: 
- Vernici Industriali
- Vernici Alimentari
- Vernici Murali Esterno
- Vernici Murali Interni

## 2. Architettura Dati e Logica
Il calcolo avviene lato server (PHP via AJAX).
- **Categorie WooCommerce:** I prodotti sono divisi nelle 4 macro-categorie.
- **Tassonomia Superfici (TAG):** Sfrutteremo una custom taxonomy non gerarchica chiamata `cv_superficie` associata al post_type `product`.
- **Resa:** Ogni prodotto candidato ha un post meta `resa_litro` (float, es. 10.5).
- **Formula Calcolo:** `Litri necessari = (Metri Quadri / resa_litro) * 1.10` (10% di tolleranza).
- **Upselling Soglia:** 30 Litri. Se il calcolo è >= 30L, consegna gratuita in loco. Se < 30L, suggerisci di aggiungere la differenza per ottenerla.

## 3. Requisiti di Sviluppo Specifici per l'Agente AI

### Step 1: Refactoring Database (IMPORTANTE)
Analizza il file `includes/class-configuratore-vernici-content-types.php`.
1. **Rimuovi** la registrazione del Custom Post Type `cv_materiale`.
2. **Aggiungi** la registrazione di una Custom Taxonomy chiamata `cv_superficie` (Labels: Superfici, Superficie). Assegnala al post type `product`. Imposta `'hierarchical' => false` (deve funzionare come i Tag).
3. Pulisci il file `admin/partials/configuratore-vernici-admin-display.php` rimuovendo il box che conteggia e linka i vecchi "Materiali" (CPT `cv_materiale`).

### Step 2: Frontend Markup & Shortcode
In `public/class-configuratore-vernici-public.php`, aggiorna il metodo dello shortcode `[configuratore_vernici]`.
Struttura l'HTML con classi compatibili con Flatsome (es. `<button class="button primary">`):
- **Dropdown 1 (Settore):** Menu select con le 4 categorie WooCommerce.
- **Dropdown 2 (Superficie):** Menu select popolato dinamicamente con i termini della tassonomia `cv_superficie`.
- **Input Number (Mq):** Superficie da verniciare (min 0.1, step 0.1).
- **Submit Button:** "Calcola Fabbisogno".
- **Risultato (Nascosto di default):** Div che mostrerà il Nome Prodotto suggerito, i Litri, il messaggio di Upsell (Successo o Avviso) e il Form per la Lead (Input Email + Textarea "Note/Aggiunte" opzionale + Submit).

### Step 3: Elaborazione Calcolo (AJAX)
Crea un endpoint AJAX in PHP (`wp_ajax_cv_calcola_prodotto` e nopriv).
- Deve ricevere: Categoria, Termine Superficie (Tag), e Mq.
- Logica: Esegui una `WP_Query` cercando 1 prodotto pubblicato che appartenga alla Categoria scelta AND abbia il tag della tassonomia `cv_superficie` scelto, e che abbia il meta `resa_litro` valorizzato.
- Calcola: Prendi la resa, applica la formula per i litri. Calcola l'upsell: se litri < 30, suggerisci di aggiungere `(30 - litri)`.
- Restituisci JSON: status success, nome prodotto, litri formattati, messaggio upsell.

### Step 4: JavaScript (Frontend)
In `public/js/configuratore-vernici-public.js`:
- Intercetta il form di calcolo: previeni il default e lancia la chiamata fetch verso l'AJAX di calcolo. Popola l'interfaccia con i risultati.
- Intercetta il form della Lead: lancia la chiamata fetch verso l'AJAX `configuratore_vernici_request_quote` inviando Email, Prodotto, Litri, Mq e le Note.

### Step 5: Elaborazione Lead (AJAX)
Aggiorna il metodo `process_quote_request` già presente per accogliere i nuovi dati in ingresso (Prodotto suggerito, Mq, Litri calcolati, Note per l'upsell) e inviarli via email tramite `wp_mail()` all'amministratore.

## 4. Regole Strict
- Mantenere la struttura OOP di WPPB.
- Usare i Nonce per la sicurezza di ogni chiamata AJAX.
- Sanitizzare input in ingresso e fare escaping in uscita.
- Scrivere codice pulito, commentato e in lingua Italiana per il frontend.