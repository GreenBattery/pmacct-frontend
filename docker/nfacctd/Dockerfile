FROM debian:bookworm-slim

RUN apt-get update && apt-get install -y --no-install-recommends \
    pmacct \
    gettext-base \
    default-mysql-client \
    tcpdump \
    nano \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /etc/pmacct

COPY docker/nfacctd/nfacctd.conf.template /etc/pmacct/nfacctd.conf.template
COPY docker/nfacctd/inbound.sql /etc/pmacct/inbound.sql
COPY docker/nfacctd/outbound.sql /etc/pmacct/outbound.sql
COPY docker/nfacctd/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 2055/udp

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
