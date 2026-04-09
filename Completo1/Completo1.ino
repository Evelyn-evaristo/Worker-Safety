#include <WiFi.h>
#include "DHT.h"

// =========================
// Wi-Fi
// =========================
const char* ssid = "nao tem senha";
const char* password = "MENTIRA TEM SENHA";

const char* API_CONFIG = "http://192.168.0.100/worker_safety/api/configuracao.php";
const char* API_SALVAR = "http://192.168.0.100/worker_safety/api/salvar_leitura.php";

unsigned long ultimaTentativaWiFi = 0;
const unsigned long intervaloReconexaoWiFi = 5000; // 5s

void conectarWiFi() {
  Serial.println("Conectando ao Wi-Fi...");
  WiFi.begin(ssid, password);

  unsigned long inicio = millis();
  const unsigned long tempoLimite = 20000; // 20s

  while (WiFi.status() != WL_CONNECTED && (millis() - inicio < tempoLimite)) {
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

// =========================
// DHT + Buzzers
// =========================
#define DHTTYPE DHT11

#define DHTPIN_A 4     // D4
#define DHTPIN_B 5     // D5

#define BUZZER_A 18    // D18
#define BUZZER_B 19    // D19

DHT dhtA(DHTPIN_A, DHTTYPE);
DHT dhtB(DHTPIN_B, DHTTYPE);

// Limites setor A
float tempMinA = 18.0;
float tempMaxA = 30.0;
float umidMinA = 40.0;
float umidMaxA = 70.0;   // CORRIGIDO: máximo 70%

// Limites setor B
float tempMinB = 18.0;
float tempMaxB = 30.0;
float umidMinB = 45.0;
float umidMaxB = 70.0;   // CORRIGIDO: máximo 70%

// Controle de leitura
unsigned long ultimaLeitura = 0;
const unsigned long intervaloLeitura = 2000; // 2s

void setup() {
  Serial.begin(115200);
  Serial.println("Booting");

  // Wi-Fi
  WiFi.mode(WIFI_STA);
  WiFi.setSleep(false);
  conectarWiFi();

  // Sensores e buzzers
  dhtA.begin();
  dhtB.begin();

  pinMode(BUZZER_A, OUTPUT);
  pinMode(BUZZER_B, OUTPUT);

  digitalWrite(BUZZER_A, LOW);
  digitalWrite(BUZZER_B, LOW);
}

void loop() {
  // Reconexão Wi-Fi automática
  if (WiFi.status() != WL_CONNECTED) {
    unsigned long agoraWiFi = millis();
    if (agoraWiFi - ultimaTentativaWiFi >= intervaloReconexaoWiFi) {
      ultimaTentativaWiFi = agoraWiFi;
      Serial.println("Wi-Fi caiu. Tentando reconectar...");
      WiFi.disconnect();
      conectarWiFi();
    }
  }

  // Leitura periódica dos sensores
  unsigned long agora = millis();
  if (agora - ultimaLeitura < intervaloLeitura) return;
  ultimaLeitura = agora;

  float tempA = dhtA.readTemperature();
  float umidA = dhtA.readHumidity();

  float tempB = dhtB.readTemperature();
  float umidB = dhtB.readHumidity();

  // ===== SETOR A =====
  Serial.println("===== SETOR A =====");
  if (isnan(tempA) || isnan(umidA)) {
    Serial.println("Erro no DHT11 A");
    digitalWrite(BUZZER_A, LOW);
  } else {
    Serial.print("Temperatura A: ");
    Serial.print(tempA);
    Serial.println(" °C");

    Serial.print("Umidade A: ");
    Serial.print(umidA);
    Serial.println(" %");

    bool alertaTempA = (tempA < tempMinA || tempA > tempMaxA);
    bool alertaUmidA = (umidA < umidMinA || umidA > umidMaxA);
    bool alertaA = alertaTempA || alertaUmidA;

    if (alertaA) {
      Serial.println("ALERTA NO SETOR A");
      if (alertaTempA) Serial.println("Motivo: Temperatura fora do limite");
      if (alertaUmidA) Serial.println("Motivo: Umidade fora do limite");
      digitalWrite(BUZZER_A, HIGH);
    } else {
      digitalWrite(BUZZER_A, LOW);
    }
  }

  // ===== SETOR B =====
  Serial.println("===== SETOR B =====");
  if (isnan(tempB) || isnan(umidB)) {
    Serial.println("Erro no DHT11 B");
    digitalWrite(BUZZER_B, LOW);
  } else {
    Serial.print("Temperatura B: ");
    Serial.print(tempB);
    Serial.println(" °C");

    Serial.print("Umidade B: ");
    Serial.print(umidB);
    Serial.println(" %");

    bool alertaTempB = (tempB < tempMinB || tempB > tempMaxB);
    bool alertaUmidB = (umidB < umidMinB || umidB > umidMaxB);
    bool alertaB = alertaTempB || alertaUmidB;

    if (alertaB) {
      Serial.println("ALERTA NO SETOR B");
      if (alertaTempB) Serial.println("Motivo: Temperatura fora do limite");
      if (alertaUmidB) Serial.println("Motivo: Umidade fora do limite");
      digitalWrite(BUZZER_B, HIGH);
    } else {
      digitalWrite(BUZZER_B, LOW);
    }
  }

  Serial.println("--------------------");
}