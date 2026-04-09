#include <WiFi.h>

const char* ssid = "EVARISTO 2.4G";
const char* password = "75694061";

unsigned long ultimoTentativa = 0;
const unsigned long intervaloReconexao = 5000;

void conectarWiFi() {
  Serial.println("Conectando ao Wi-Fi...");
  WiFi.begin(ssid, password);

  unsigned long inicio = millis();
  const unsigned long tempoLimite = 20000;

  while (WiFi.status() != WL_CONNECTED && millis() - inicio < tempoLimite) {
    delay(500);
    Serial.print(".");
  }

  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\nConectado!");
    Serial.print("IP: ");
    Serial.println(WiFi.localIP());
  } else {
    Serial.println("\nNão conectou.");
    Serial.println("Confira nome, senha e se a rede é 2.4 GHz.");
  }
}

void setup() {
  Serial.begin(115200);
  Serial.println("Booting");

  WiFi.mode(WIFI_STA);
  WiFi.setSleep(false); // remova se quiser economizar energia

  conectarWiFi();
}

void loop() {
  if (WiFi.status() != WL_CONNECTED) {
    unsigned long agora = millis();
    if (agora - ultimoTentativa >= intervaloReconexao) {
      ultimoTentativa = agora;
      Serial.println("Wi-Fi caiu. Tentando reconectar...");
      WiFi.disconnect();
      conectarWiFi();
    }
  }
}