const btnSidebar = document.getElementById("btnSidebar");
const sidebar = document.getElementById("sidebar");
const menuItems = document.querySelectorAll(".menu-item");
const menuLinks = document.querySelectorAll(".menu-item a[href$='.html']");
const menuConfiguracoes = document.getElementById("menuConfiguracoes");
const botaoConfiguracoes = menuConfiguracoes
    ? menuConfiguracoes.querySelector(".menu-button-item")
    : null;

function aplicarModoInicial() {
    if (window.innerWidth <= 768) {
        sidebar.classList.remove("closed");
        sidebar.classList.remove("open");
    } else {
        sidebar.classList.remove("open");
        sidebar.classList.remove("closed");
    }
}

function alternarSidebar() {
    if (window.innerWidth <= 768) {
        sidebar.classList.toggle("open");
    } else {
        sidebar.classList.toggle("closed");
    }
}

function limparAtivo() {
    menuItems.forEach(function (item) {
        item.classList.remove("active");
    });
}

function atualizarItemAtivoPorPagina() {
    const paginaAtual = window.location.pathname.split("/").pop() || "index.html";

    limparAtivo();

    menuLinks.forEach(function (link) {
        const hrefLink = link.getAttribute("href");

        if (hrefLink === paginaAtual) {
            const itemPai = link.closest(".menu-item");
            if (itemPai) {
                itemPai.classList.add("active");
            }
        }
    });
}

function controlarSubmenu() {
    if (!botaoConfiguracoes || !menuConfiguracoes) {
        return;
    }

    botaoConfiguracoes.addEventListener("click", function () {
        if (sidebar.classList.contains("closed")) {
            sidebar.classList.remove("closed");
            return;
        }

        if (window.innerWidth <= 768 && !sidebar.classList.contains("open")) {
            sidebar.classList.add("open");
            return;
        }

        menuConfiguracoes.classList.toggle("open");
    });
}

function fecharSubmenuQuandoFecharSidebar() {
    if (!menuConfiguracoes) {
        return;
    }

    if (sidebar.classList.contains("closed")) {
        menuConfiguracoes.classList.remove("open");
    }

    if (window.innerWidth <= 768 && !sidebar.classList.contains("open")) {
        menuConfiguracoes.classList.remove("open");
    }
}

if (btnSidebar && sidebar) {
    btnSidebar.addEventListener("click", function () {
        alternarSidebar();
        fecharSubmenuQuandoFecharSidebar();
    });
}

window.addEventListener("resize", function () {
    aplicarModoInicial();
    fecharSubmenuQuandoFecharSidebar();
});

controlarSubmenu();
aplicarModoInicial();
atualizarItemAtivoPorPagina();

/* GRÁFICO */
const ctx = document.getElementById("tempChart");
const tempSpan = document.getElementById("temp");
const humSpan = document.getElementById("hum");

if (ctx && tempSpan && humSpan) {
    const chart = new Chart(ctx, {
        type: "line",
        data: {
            labels: [],
            datasets: [{
                label: "Temperatura",
                data: [],
                borderColor: "#a3ff12",
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    setInterval(function () {
        let temp = Math.floor(Math.random() * 10) + 20;
        let hum = Math.floor(Math.random() * 30) + 40;

        tempSpan.innerText = temp;
        humSpan.innerText = hum;

        chart.data.labels.push("");
        chart.data.datasets[0].data.push(temp);

        if (chart.data.labels.length > 10) {
            chart.data.labels.shift();
            chart.data.datasets[0].data.shift();
        }

        chart.update();
    }, 2000);
}