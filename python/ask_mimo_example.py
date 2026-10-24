import os
from openai import OpenAI

client = OpenAI(
    api_key=os.getenv("AI_API_KEY") or os.getenv("OPENAI_API_KEY"),
    base_url=os.getenv("AI_BASE_URL", "https://api.openai.com/v1"),
)

model = os.getenv("MIMO_MODEL", "mimo-v2.5")

# Simple call
response = client.chat.completions.create(
    model=model,
    messages=[
        {"role": "user", "content": "Search the web for today's top AI story and summarize it"}
    ],
)
print(response.choices[0].message.content)

# With system prompt and raw details
result = client.chat.completions.create(
    model=model,
    messages=[
        {"role": "system", "content": "You are a helpful assistant."},
        {"role": "user", "content": "Summarize https://peps.python.org/pep-0008/"},
    ],
)
text = result.choices[0].message.content
usage = result.usage
print(f"\n{text}")
print(f"Tokens used: {usage.prompt_tokens} + {usage.completion_tokens}")

# Stream
stream = client.chat.completions.create(
    model=model,
    messages=[
        {"role": "user", "content": "Write a short poem about automation"}
    ],
    stream=True,
)
print("\n")
for chunk in stream:
    if chunk.choices[0].delta.content:
        print(chunk.choices[0].delta.content, end="", flush=True)
print()
