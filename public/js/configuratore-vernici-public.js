(function() {
	'use strict';

	var settings = window.configuratoreVernici || {
		ajaxUrl: '',
		calculationNonce: '',
		quoteNonce: '',
		nonce: '',
		freeDeliveryThreshold: 30,
		texts: {
			selectCategory: 'Seleziona il settore.',
			selectSurface: 'Seleziona il materiale o supporto da verniciare.',
			selectProduct: 'Seleziona un prodotto valido.',
			enterMq: 'Inserisci i metri quadri da verniciare.',
			enterEmail: 'Inserisci un indirizzo email valido.',
			genericError: 'Si e verificato un errore. Riprova piu tardi.',
			loading: 'Calcolo in corso...',
			quoteLoading: 'Invio in corso...',
			featured: 'Consigliato'
		}
	};

	function parseNumber(value) {
		if (typeof value !== 'string') {
			return Number(value) || 0;
		}

		return Number(value.replace(',', '.')) || 0;
	}

	function setMessage(container, message, type) {
		if (!container) {
			return;
		}

		container.textContent = message || '';
		container.classList.remove('is-error', 'is-success');

		if (type) {
			container.classList.add('is-' + type);
		}
	}

	function setButtonLoading(button, isLoading, label) {
		if (!button) {
			return;
		}

		if (!button.dataset.originalText) {
			button.dataset.originalText = button.textContent;
		}

		button.disabled = isLoading;
		button.textContent = isLoading ? label : button.dataset.originalText;
	}

	function readJson(response) {
		return response.json().then(function(payload) {
			if (!response.ok || !payload || !payload.success) {
				throw new Error(payload && payload.data && payload.data.message ? payload.data.message : settings.texts.genericError);
			}

			return payload.data;
		});
	}

	function initConfigurator(root) {
		var calculationForm = root.querySelector('[data-configuratore-calcolo]');
		var quoteForm = root.querySelector('[data-configuratore-preventivo]');
		var categoryField = root.querySelector('[data-configuratore-categoria]');
		var surfaceField = root.querySelector('[data-configuratore-superficie]');
		var mqField = root.querySelector('[data-configuratore-mq]');
		var emailField = root.querySelector('[data-configuratore-email]');
		var notesField = root.querySelector('[data-configuratore-note]');
		var resultBox = root.querySelector('[data-configuratore-risultato]');
		var productsBox = root.querySelector('[data-configuratore-prodotti]');
		var deliveryMessage = root.querySelector('[data-configuratore-consegna]');
		var messageBox = root.querySelector('[data-configuratore-messaggio]');
		var selectedProduct = null;
		var productRadioName = 'configuratore_vernici_prodotto_' + Math.random().toString(36).slice(2);

		if (!calculationForm || !quoteForm || !categoryField || !surfaceField || !mqField || !productsBox) {
			return;
		}

		function clearResults() {
			selectedProduct = null;
			productsBox.innerHTML = '';
			resultBox.hidden = true;
			quoteForm.hidden = true;
			if (deliveryMessage) {
				deliveryMessage.textContent = '';
				deliveryMessage.classList.remove('is-success', 'is-warning');
			}
		}

		function updateDelivery(product) {
			if (!deliveryMessage || !product) {
				return;
			}

			var threshold = parseNumber(String(settings.freeDeliveryThreshold || '30'));
			var liters = parseNumber(String(product.litri || '0'));

			deliveryMessage.textContent = product.upsell_message || '';
			deliveryMessage.classList.remove('is-success', 'is-warning');
			deliveryMessage.classList.add(liters >= threshold ? 'is-success' : 'is-warning');
		}

		function renderProducts(products) {
			productsBox.innerHTML = '';
			selectedProduct = null;

			products.forEach(function(product, index) {
				var card = document.createElement('label');
				var radio = document.createElement('input');
				var body = document.createElement('span');
				var title = document.createElement('strong');
				var details = document.createElement('span');

				card.className = 'configuratore-vernici__product';
				body.className = 'configuratore-vernici__product-body';
				details.className = 'configuratore-vernici__product-details';

				radio.type = 'radio';
				radio.name = productRadioName;
				radio.value = product.id;
				radio.checked = index === 0;

				title.textContent = product.name || '';
				details.textContent = 'Litri stimati: ' + product.litri_formattati + ' L - Resa: ' + product.resa_litro + ' m2/L';

				if (product.is_featured) {
					var badge = document.createElement('span');
					badge.className = 'configuratore-vernici__badge';
					badge.textContent = settings.texts.featured;
					body.appendChild(badge);
				}

				body.appendChild(title);
				body.appendChild(details);
				card.appendChild(radio);
				card.appendChild(body);
				productsBox.appendChild(card);

				radio.addEventListener('change', function() {
					if (radio.checked) {
						selectedProduct = product;
						updateDelivery(product);
					}
				});

				if (index === 0) {
					selectedProduct = product;
				}
			});

			updateDelivery(selectedProduct);
			resultBox.hidden = false;
			quoteForm.hidden = false;
		}

		function validateInputs() {
			if (!categoryField.value) {
				setMessage(messageBox, settings.texts.selectCategory, 'error');
				return false;
			}

			if (!surfaceField.value) {
				setMessage(messageBox, settings.texts.selectSurface, 'error');
				return false;
			}

			if (parseNumber(mqField.value) <= 0) {
				setMessage(messageBox, settings.texts.enterMq, 'error');
				return false;
			}

			return true;
		}

		calculationForm.addEventListener('submit', function(event) {
			event.preventDefault();

			if (!validateInputs()) {
				clearResults();
				return;
			}

			var submitButton = calculationForm.querySelector('button[type="submit"]');
			var formData = new FormData();

			formData.append('action', 'cv_calcola_prodotto');
			formData.append('nonce', settings.calculationNonce);
			formData.append('categoria', categoryField.value);
			formData.append('superficie', surfaceField.value);
			formData.append('mq', mqField.value);

			clearResults();
			setMessage(messageBox, '', null);
			setButtonLoading(submitButton, true, settings.texts.loading);

			fetch(settings.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				body: formData
			})
				.then(readJson)
				.then(function(data) {
					renderProducts(data.products || []);
				})
				.catch(function(error) {
					setMessage(messageBox, error.message || settings.texts.genericError, 'error');
				})
				.finally(function() {
					setButtonLoading(submitButton, false, settings.texts.loading);
				});
		});

		[categoryField, surfaceField, mqField].forEach(function(field) {
			field.addEventListener(field === mqField ? 'input' : 'change', function() {
				clearResults();
				setMessage(messageBox, '', null);
			});
		});

		quoteForm.addEventListener('submit', function(event) {
			event.preventDefault();

			if (!selectedProduct) {
				setMessage(messageBox, settings.texts.selectProduct, 'error');
				return;
			}

			if (!emailField || !emailField.value.trim() || !emailField.checkValidity()) {
				setMessage(messageBox, settings.texts.enterEmail, 'error');
				return;
			}

			var submitButton = quoteForm.querySelector('button[type="submit"]');
			var formData = new FormData();

			formData.append('action', 'configuratore_vernici_request_quote');
			formData.append('nonce', settings.quoteNonce || settings.nonce);
			formData.append('email', emailField.value.trim());
			formData.append('product_id', selectedProduct.id);
			formData.append('product', selectedProduct.name);
			formData.append('category_id', categoryField.value);
			formData.append('surface_id', surfaceField.value);
			formData.append('mq', mqField.value);
			formData.append('litri', selectedProduct.litri);
			formData.append('notes', notesField ? notesField.value : '');

			setMessage(messageBox, '', null);
			setButtonLoading(submitButton, true, settings.texts.quoteLoading);

			fetch(settings.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				body: formData
			})
				.then(readJson)
				.then(function(data) {
					setMessage(messageBox, data.message, 'success');
					quoteForm.reset();
					quoteForm.hidden = true;
				})
				.catch(function(error) {
					setMessage(messageBox, error.message || settings.texts.genericError, 'error');
				})
				.finally(function() {
					setButtonLoading(submitButton, false, settings.texts.quoteLoading);
				});
		});
	}

	document.addEventListener('DOMContentLoaded', function() {
		document.querySelectorAll('[data-configuratore-vernici]').forEach(initConfigurator);
	});
})();
