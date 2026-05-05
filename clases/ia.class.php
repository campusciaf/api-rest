<?php

class IA {
//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
    private $apiKey = "";

    public function generarRespuesta($mensaje){

        $data = [
            "model" => "gpt-4o-mini",
            "messages" => [
                [
                    "role" => "system",
                    "content" => "Eres Craia, asesora académica de CIAF. Responde claro, breve y comercial."
                ],
                [
                    "role" => "user",
                    "content" => $mensaje
                ]
            ]
        ];

        $ch = curl_init("https://api.openai.com/v1/chat/completions");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . $this->apiKey
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        return $result['choices'][0]['message']['content'] ?? "No pude responder en este momento.";
    }
}