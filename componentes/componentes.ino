#include "DHT.h"

#define DHTTYPE DHT11

#define DHTPIN_A 4     // D4
#define DHTPIN_B 5     // D5

#define BUZZER_A 18    // D18
#define BUZZER_B 19    // D19

DHT dhtA(DHTPIN_A, DHTTYPE);
DHT dhtB(DHTPIN_B, DHTTYPE);

// Limites setor A
float tempMinA = 18.0;
float tempMaxA = 24.0;
float umidMinA = 40.0;
float umidMaxA = 60.0;

// Limites setor B
float tempMinB = 18.0;
float tempMaxB = 22.0;
float umidMinB = 45.0;
float umidMaxB = 55.0;

void setup() {
  Serial.begin(115200);

  dhtA.begin();
  dhtB.begin();

  pinMode(BUZZER_A, OUTPUT);
  pinMode(BUZZER_B, OUTPUT);

  digitalWrite(BUZZER_A, LOW);
  digitalWrite(BUZZER_B, LOW);
}

void loop() {
  float tempA = dhtA.readTemperature();
  float umidA = dhtA.readHumidity();

  float tempB = dhtB.readTemperature();
  float umidB = dhtB.readHumidity();

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

    bool alertaA = (tempA < tempMinA || tempA > tempMaxA ||
                    umidA < umidMinA || umidA > umidMaxA);

    if (alertaA) {
      Serial.println("ALERTA NO SETOR A");
      digitalWrite(BUZZER_A, HIGH);
    } else {
      digitalWrite(BUZZER_A, LOW);
    }
  }

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

    bool alertaB = (tempB < tempMinB || tempB > tempMaxB ||
                    umidB < umidMinB || umidB > umidMaxB);

    if (alertaB) {
      Serial.println("ALERTA NO SETOR B");
      digitalWrite(BUZZER_B, HIGH);
    } else {
      digitalWrite(BUZZER_B, LOW);
    }
  }

  Serial.println("--------------------");
  delay(2000);
}