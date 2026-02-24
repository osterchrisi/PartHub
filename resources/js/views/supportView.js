export function initializeSupportView() {
    const user = "hello"; // Replace with your email's user part
    const domain = "parthub.online"; // Replace with your domain
    const email = `${user}@${domain}`;
    const container = document.getElementById("email");
    if (!container) return;

    container.replaceChildren();

    const a = document.createElement("a");
    a.href = `mailto:${email}`;
    a.textContent = email;

    container.appendChild(a);
};