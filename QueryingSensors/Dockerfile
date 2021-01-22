FROM node:12
WORKDIR /app
COPY package.json /app
RUN npm install
RUN npm i mongo-query-generator
COPY . .
EXPOSE 1024
CMD [ "node", "mongoparser.js" ]
