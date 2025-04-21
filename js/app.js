const res = await fetch('/api/handler.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify(formData)
});

if (res.status === 201) {
    alert('User created successfully!');
} else if (res.status === 400) {
    alert('Bad Request: Please check your input.');
} else {
    const data = await res.json();
    alert('Error: ' + data.message);
}
