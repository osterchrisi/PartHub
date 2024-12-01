export function initializeSupportView() {
    console.log("tis support");
    const user = "hello"; // Replace with your email's user part
            const domain = "parthub.online"; // Replace with your domain
            const email = `${user}@${domain}`;
            const emailLink = `<a href="mailto:${email}">${email}</a>`;
            document.getElementById("email").innerHTML = emailLink;
};