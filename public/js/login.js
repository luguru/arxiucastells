document.getElementById('login-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const errorMessage = document.getElementById('error-message');
            errorMessage.classList.add('hidden');

            const formData = new FormData(this);
            formData.append('action', 'login');

            try {
                // La ruta correcta a la API
                const response = await fetch('../src/api/index.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    window.location.href = 'index.php';
                } else {
                    errorMessage.querySelector('span').textContent = result.error || 'Usuario o contraseña incorrectos.';
                    errorMessage.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error de red:', error);
                errorMessage.querySelector('span').textContent = 'Error de conexión. Inténtelo de nuevo.';
                errorMessage.classList.remove('hidden');
            }
        });