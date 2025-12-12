import express from "express";

const app = express();
app.use(express.json());

// ضع مفتاحك كـ Environment Variable: OPENAI_API_KEY
const OPENAI_API_KEY = process.env.OPENAI_API_KEY;

app.post("/api/chat", async (req, res) => {
  try {
    const userText = (req.body?.text || "").toString().trim();
    if (!userText) return res.status(400).json({ error: "Missing text" });

    // ردود قصيرة مثل طلبك
    const instructions = "أجب بإجابة عربية قصيرة جدًا (جملة واحدة أو سطرين).";

    const r = await fetch("https://api.openai.com/v1/responses", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${OPENAI_API_KEY}`, // Bearer auth :contentReference[oaicite:4]{index=4}
      },
      body: JSON.stringify({
        model: "gpt-5.2-chat-latest",
        instructions,
        input: userText,
      }),
    });

    const data = await r.json();
    if (!r.ok) return res.status(r.status).json(data);

    // Responses API تعطي النص الجاهز في output_text غالبًا
    res.json({ reply: data.output_text || "لم أستطع الرد." });
  } catch (e) {
    res.status(500).json({ error: "Server error" });
  }
});

app.listen(3000, () => console.log("Server on http://localhost:3000"));
