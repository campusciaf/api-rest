<?php

class IA {

    private $apiKey = "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";

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